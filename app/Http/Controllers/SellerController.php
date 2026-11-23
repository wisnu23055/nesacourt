<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\PaymentConfirmation;
use App\Models\PredictionLog;
use App\Models\SellerPaymentQris;
use App\Models\SellerProfile;
use App\Services\NesaCourtAiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerController extends Controller
{
    /**
     * Resolusi Stan Penjual aktif dari user yang login
     */
    protected function getSeller(): SellerProfile
    {
        $user = Auth::user();
        if ($user && $user->sellerProfile) {
            return $user->sellerProfile;
        }

        // Fallback untuk akun admin yang mengelola stan
        return SellerProfile::first() ?? SellerProfile::create([
            'user_id' => $user->id,
            'stall_name' => 'Stan 01 - Barokah Rasa',
            'slug' => 'barokah',
            'location' => 'Counter Barat No. 01',
            'is_active' => true,
        ]);
    }

    /**
     * F-15: Dashboard Stan Penjual
     */
    public function dashboard(): View
    {
        $seller = $this->getSeller();

        $today = now()->startOfDay();
        $completedOrdersToday = Order::where('seller_id', $seller->id)
            ->where('order_status', 'selesai')
            ->where('created_at', '>=', $today)
            ->get();

        $omsetToday = (int) $completedOrdersToday->sum('total');
        $ordersCountToday = Order::where('seller_id', $seller->id)
            ->where('created_at', '>=', $today)
            ->count();
        $completedCountToday = $completedOrdersToday->count();

        $portionsSoldToday = (int) OrderItem::whereIn('order_id', $completedOrdersToday->pluck('id'))->sum('quantity');
        $lowStockCount = Menu::where('seller_id', $seller->id)->where('is_active', true)->where('stock', '<=', 3)->count();

        $activeOrders = Order::with(['buyer', 'items'])
            ->where('seller_id', $seller->id)
            ->whereIn('order_status', ['dibuat', 'diterima', 'diproses', 'siap'])
            ->latest()
            ->take(5)
            ->get();

        $activeOrdersData = $activeOrders->map(function (Order $o) {
            $statusKey = match ($o->order_status) {
                'dibuat' => 'menunggu_konfirmasi',
                'diterima', 'diproses' => 'memasak',
                'siap' => 'siap_ambil',
                'selesai' => 'selesai',
                default => 'menunggu_konfirmasi',
            };

            $statusLabel = match ($statusKey) {
                'menunggu_konfirmasi' => 'Menunggu Verifikasi',
                'memasak' => 'Sedang Dimasak',
                'siap_ambil' => 'Siap Diambil',
                'selesai' => 'Pesanan Selesai',
            };

            return [
                'id' => $o->order_number,
                'queueNo' => str_pad($o->id % 100, 2, '0', STR_PAD_LEFT),
                'customerName' => $o->customer_name,
                'initials' => strtoupper(substr($o->customer_name, 0, 2)),
                'time' => $o->created_at->format('H:i').' WIB',
                'status' => $statusKey,
                'statusLabel' => $statusLabel,
                'totalPrice' => (int) $o->total,
                'paymentMethod' => 'QRIS',
                'items' => $o->items->map(fn (OrderItem $it) => [
                    'name' => $it->menu_name_snapshot,
                    'qty' => $it->quantity,
                    'price' => (int) $it->menu_price_snapshot,
                    'subtotal' => (int) $it->subtotal,
                    'note' => $it->note ?? '',
                ])->values()->all(),
            ];
        })->values();

        $popularMenus = Menu::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->orderByDesc('stock')
            ->take(3)
            ->get();

        return view('seller.dashboard', [
            'seller' => $seller,
            'omsetToday' => $omsetToday,
            'ordersCountToday' => $ordersCountToday,
            'completedCountToday' => $completedCountToday,
            'portionsSoldToday' => $portionsSoldToday,
            'lowStockCount' => $lowStockCount,
            'activeOrders' => $activeOrders,
            'activeOrdersData' => $activeOrdersData,
            'popularMenus' => $popularMenus,
        ]);
    }

    /**
     * F-16: Daftar Katalog Menu Stan
     */
    public function menuIndex(): View
    {
        $seller = $this->getSeller();
        $menus = Menu::with('category')
            ->where('seller_id', $seller->id)
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(fn (Menu $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category->slug ?? 'makanan',
                'categoryLabel' => $m->category->name ?? 'Makanan',
                'price' => (int) $m->price,
                'stock' => (int) ($m->stock ?? 0),
                'isAvailable' => (bool) $m->is_available,
                'isTopSeller' => (bool) ($m->stock > 10),
                'image' => $m->image_url,
                'desc' => $m->description ?? '',
            ])
            ->values();

        return view('seller.menu.index', [
            'seller' => $seller,
            'menus' => $menus,
        ]);
    }

    /**
     * F-17: Form Tambah / Edit Menu
     */
    public function menuForm(Request $request): View
    {
        $seller = $this->getSeller();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $menu = null;
        $menuId = $request->route('id') ?? $request->query('id'); // ponytail: rute /menu/{id}/edit + link lama ?id=
        if ($menuId) {
            $menu = Menu::where('seller_id', $seller->id)->find($menuId);
        }

        return view('seller.menu.form', [
            'seller' => $seller,
            'categories' => $categories,
            'menu' => $menu,
        ]);
    }

    /**
     * Simpan / Perbarui Menu (CRUD)
     */
    public function menuStore(Request $request): JsonResponse|RedirectResponse
    {
        $seller = $this->getSeller();

        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:500'],
            'stock' => ['required', 'integer', 'min:0'],
            'estimated_time_minutes' => ['nullable', 'integer', 'min:1'],
            'prepTime' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:tersedia,habis'],
            'is_available' => ['nullable'],
            'description' => ['nullable', 'string', 'max:500'],
            'desc' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $prepTime = $validated['estimated_time_minutes'] ?? $validated['prepTime'] ?? 10;
        $description = $validated['description'] ?? $validated['desc'] ?? '';
        $isAvailable = ($request->input('status', 'tersedia') === 'tersedia') && ($validated['stock'] > 0);

        $category = Category::where('slug', $validated['category'])->first()
            ?? Category::firstOrCreate(['slug' => $validated['category']], ['name' => ucfirst($validated['category']), 'sort_order' => 99]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $imagePath = Storage::url($path);
        }

        if (! empty($validated['id'])) {
            $menu = Menu::where('seller_id', $seller->id)->findOrFail($validated['id']);
            $updateData = [
                'category_id' => $category->id,
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'estimated_time_minutes' => $prepTime,
                'is_available' => $isAvailable,
                'description' => $description,
            ];
            if ($imagePath) {
                $updateData['image_path'] = $imagePath;
            }
            $menu->update($updateData);
        } else {
            $menu = Menu::create([
                'seller_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'estimated_time_minutes' => $prepTime,
                'is_available' => $isAvailable,
                'is_active' => true,
                'description' => $description,
                'image_path' => $imagePath ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'taste_tags' => ['Lezat', 'Kantin'],
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'menu' => $menu]);
        }

        return redirect()->route('seller.menu.index')->with('success', 'Menu berhasil disimpan.');
    }

    /**
     * Hapus Menu
     */
    public function menuDestroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $seller = $this->getSeller();
        $menu = Menu::where('seller_id', $seller->id)->findOrFail($id);

        if ($menu->orderItems()->count() === 0) {
            if ($menu->image_path && !str_starts_with($menu->image_path, 'http')) {
                $filePath = str_replace('/storage/', '', $menu->image_path);
                Storage::disk('public')->delete($filePath);
            }
            $menu->delete();
        } else {
            $menu->update(['is_active' => false]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('seller.menu.index')->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * F-18: Manajemen Stok Porsi Harian
     */
    public function stockIndex(): View
    {
        $seller = $this->getSeller();
        $items = Menu::with('category')
            ->where('seller_id', $seller->id)
            ->where('is_active', true)
            ->get()
            ->map(fn (Menu $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category->slug ?? 'makanan',
                'categoryLabel' => $m->category->name ?? 'Makanan',
                'price' => (int) $m->price,
                'stock' => (int) ($m->stock ?? 0),
                'image' => $m->image_url,
                'desc' => $m->description ?? '',
            ])
            ->values();

        return view('seller.stock', [
            'seller' => $seller,
            'items' => $items,
        ]);
    }

    /**
     * Simpan Perubahan Stok Porsi
     */
    public function stockUpdate(Request $request): JsonResponse|RedirectResponse
    {
        $seller = $this->getSeller();

        $items = $request->input('stocks', $request->input('items', []));
        $request->merge(['items' => $items]);

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.stock' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['items'] as $itemData) {
            $stock = (int) $itemData['stock'];
            Menu::where('seller_id', $seller->id)
                ->where('id', $itemData['id'])
                ->update([
                    'stock' => $stock,
                    'is_available' => $stock > 0,
                ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('stock_success', 'Stok porsi berhasil diperbarui.');
    }

    /**
     * F-19: Pesanan Masuk Dapur
     */
    public function ordersIndex(): View
    {
        $seller = $this->getSeller();

        $orders = Order::with(['buyer', 'items', 'statusLogs'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->get()
            ->map(function (Order $o) {
                $statusKey = match ($o->order_status) {
                    'dibuat' => ($o->payment_status === 'perlu_pemeriksaan' ? 'menunggu_konfirmasi' : 'menunggu_konfirmasi'),
                    'diterima', 'diproses' => 'memasak',
                    'siap' => 'siap_ambil',
                    'selesai' => 'selesai',
                    default => 'selesai',
                };

                $statusLabel = match ($statusKey) {
                    'menunggu_konfirmasi' => 'Menunggu Verifikasi',
                    'memasak' => 'Sedang Dimasak',
                    'siap_ambil' => 'Siap Diambil',
                    'selesai' => 'Pesanan Selesai',
                };

                $diningType = str_contains($o->note ?? '', 'Bawa Pulang') ? 'Bungkus / Bawa Pulang' : 'Makan di Tempat';

                return [
                    'id' => $o->order_number,
                    'queueNo' => str_pad($o->id % 100, 2, '0', STR_PAD_LEFT),
                    'customerName' => $o->customer_name,
                    'customerType' => 'Mahasiswa Vokasi',
                    'diningType' => $diningType,
                    'time' => $o->created_at->format('H:i').' WIB',
                    'status' => $statusKey,
                    'rawStatus' => $o->order_status,
                    'statusLabel' => $statusLabel,
                    'paymentMethod' => 'QRIS',
                    'isPaid' => in_array($o->payment_status, ['terkonfirmasi', 'perlu_pemeriksaan'], true),
                    'totalPrice' => (int) $o->total,
                    'notes' => $o->note ?? '',
                    'items' => $o->items->map(fn (OrderItem $it) => [
                        'name' => $it->menu_name_snapshot,
                        'qty' => $it->quantity,
                        'price' => (int) $it->menu_price_snapshot,
                        'note' => $it->note ?? '',
                    ])->values()->all(),
                ];
            })
            ->values();

        return view('seller.orders.index', [
            'seller' => $seller,
            'orders' => $orders,
        ]);
    }

    /**
     * F-20: Detail Pesanan & Kendali Status Dapur
     */
    public function ordersDetail(Request $request): View
    {
        $seller = $this->getSeller();
        $orderId = $request->query('id');

        $order = null;
        if ($orderId) {
            $order = Order::with(['buyer', 'items', 'statusLogs'])
                ->where('seller_id', $seller->id)
                ->where('order_number', $orderId)
                ->first();
        }

        if (! $order) {
            $order = Order::with(['buyer', 'items', 'statusLogs'])
                ->where('seller_id', $seller->id)
                ->latest()
                ->first();
        }

        $step = 1;
        $statusKey = 'menunggu_konfirmasi';
        $statusLabel = 'Menunggu Verifikasi';

        if ($order) {
            if ($order->order_status === 'selesai') {
                $step = 4;
                $statusKey = 'selesai';
                $statusLabel = 'Pesanan Selesai';
            } elseif ($order->order_status === 'siap') {
                $step = 3;
                $statusKey = 'siap_ambil';
                $statusLabel = 'Siap Diambil';
            } elseif (in_array($order->order_status, ['diproses', 'diterima'], true)) {
                $step = 2;
                $statusKey = 'memasak';
                $statusLabel = 'Sedang Dimasak';
            } else {
                $step = 1;
                $statusKey = 'menunggu_konfirmasi';
                $statusLabel = 'Menunggu Verifikasi';
            }
        }

        return view('seller.orders.detail', [
            'seller' => $seller,
            'order' => $order,
            'step' => $step,
            'currentStep' => $step,
            'statusKey' => $statusKey,
            'statusLabel' => $statusLabel,
        ]);
    }

    /**
     * Update Status Pesanan dari Dapur Penjual
     */
    public function updateOrderStatus(Request $request, string $order_number): JsonResponse|RedirectResponse
    {
        $seller = $this->getSeller();
        $order = Order::where('order_number', $order_number)
            ->where('seller_id', $seller->id)
            ->firstOrFail();

        $statusInput = $request->input('order_status');
        if ($statusInput === 'memasak') {
            $request->merge(['order_status' => 'diproses']);
        } elseif ($statusInput === 'siap_ambil') {
            $request->merge(['order_status' => 'siap']);
        }

        $validated = $request->validate([
            'order_status' => ['required', 'in:diterima,diproses,siap,selesai,dibatalkan'],
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        // ponytail: jika dibatalkan oleh penjual, rollback stok via method atomik model Order
        if ($newStatus === 'dibatalkan') {
            $order->cancelAndRestoreStock(Auth::id(), 'seller', 'Pesanan dibatalkan oleh pihak stan kantin.');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'order_status' => 'dibatalkan']);
            }

            return back()->with('success', 'Pesanan dibatalkan dan stok porsi telah dikembalikan.');
        }

        $updateData = ['order_status' => $newStatus];
        if ($newStatus === 'selesai' || $newStatus === 'diproses') {
            if ($order->payment_status === 'perlu_pemeriksaan') {
                $updateData['payment_status'] = 'terkonfirmasi';

                // Catat konfirmasi pembayaran
                PaymentConfirmation::create([
                    'order_id' => $order->id,
                    'seller_id' => $seller->id,
                    'confirmed_by' => Auth::id(),
                    'amount' => $order->total,
                    'confirmed_at' => now(),
                    'merchant_reference' => 'QRIS-' . $order->order_number,
                ]);
            }
        }

        $order->update($updateData);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'actor_user_id' => Auth::id(),
            'actor_type' => 'seller',
            'old_order_status' => $oldStatus,
            'new_order_status' => $newStatus,
            'old_payment_status' => $order->payment_status,
            'new_payment_status' => $order->payment_status,
            'note' => "Status diubah menjadi {$newStatus} oleh pihak stan",
            'created_at' => now(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'order_status' => $newStatus]);
        }

        return back()->with('status_success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * F-21: Pengaturan QRIS Stan
     */
    public function qrisIndex(): View
    {
        $seller = $this->getSeller();
        $qris = $seller->qris ?? SellerPaymentQris::firstOrCreate(
            ['seller_id' => $seller->id],
            ['image_path' => '/images/qris.jpeg', 'merchant_name' => $seller->stall_name, 'is_active' => true]
        );

        return view('seller.qris', [
            'seller' => $seller,
            'qris' => $qris,
        ]);
    }

    /**
     * Simpan Pengaturan QRIS & Rekening Stan
     */
    public function qrisUpdate(Request $request): RedirectResponse
    {
        $seller = $this->getSeller();

        $validated = $request->validate([
            'merchant_name' => ['nullable', 'string', 'max:100'],
            'qris_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $qris = $seller->qris ?? new SellerPaymentQris(['seller_id' => $seller->id]);

        if ($request->hasFile('qris_image')) {
            $path = $request->file('qris_image')->store('qris', 'public');
            $qris->image_path = Storage::url($path);
        }

        if (! empty($validated['merchant_name'])) {
            $qris->merchant_name = $validated['merchant_name'];
        }

        $qris->is_active = true;
        $qris->save();

        return redirect()->route('seller.qris')->with('qris_success', 'Pengaturan QRIS berhasil disimpan.');
    }

    /**
     * F-22: Laporan Penjualan & Omset
     */
    public function reportsIndex(Request $request): View|StreamedResponse
    {
        $seller = $this->getSeller();

        $period = $request->query('period', 'hari_ini');
        $query = Order::with(['buyer', 'items'])->where('seller_id', $seller->id)->where('order_status', 'selesai');

        if ($period === 'hari_ini') {
            $query->where('created_at', '>=', now()->startOfDay());
        } elseif ($period === '7_hari') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === 'bulan_ini') {
            $query->where('created_at', '>=', now()->startOfMonth());
        } elseif ($period === 'semester') {
            $query->where('created_at', '>=', now()->subMonths(6));
        }

        $completedOrders = $query->latest()->get();

        $totalRevenue = (int) $completedOrders->sum('total');
        $ordersCount = $completedOrders->count();
        $portionsCount = (int) OrderItem::whereIn('order_id', $completedOrders->pluck('id'))->sum('quantity');
        $avgTicket = $ordersCount > 0 ? (int) ($totalRevenue / $ordersCount) : 0;

        $topMenus = Menu::where('seller_id', $seller->id)->orderByDesc('stock')->take(3)->get();
        $recentTransactions = $completedOrders->take(10);

        if ($request->query('export') === 'excel' || $request->query('export') === 'csv') {
            $stallName = str_replace(' ', '_', $seller->stall_name ?? 'Stan');
            $filename = 'Laporan_Penjualan_' . $stallName . '_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $periodLabel = match($period) {
                'hari_ini' => 'Hari Ini (' . now()->translatedFormat('d F Y') . ')',
                '7_hari' => '7 Hari Terakhir',
                'bulan_ini' => 'Bulan Ini (' . now()->translatedFormat('F Y') . ')',
                'semester' => 'Semester Ganjil 2026/2027',
                default => 'Semua Periode',
            };

            return response()->stream(function () use ($completedOrders, $seller, $periodLabel, $totalRevenue, $ordersCount, $portionsCount, $avgTicket) {
                $handle = fopen('php://output', 'w');
                fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM agar rapi saat dibuka di Microsoft Excel
                fputcsv($handle, ['REKAPITULASI LAPORAN PENJUALAN KASIR STAN KANTIN']);
                fputcsv($handle, ['Nama Stan', $seller->stall_name ?? 'Stan Kantin']);
                fputcsv($handle, ['Pemilik / Kasir', $seller->user?->name ?? '-']);
                fputcsv($handle, ['Lokasi Counter', $seller->location ?? 'Food Court UNESA']);
                fputcsv($handle, ['Periode Laporan', $periodLabel]);
                fputcsv($handle, ['Waktu Unduh', now()->translatedFormat('d F Y, H:i') . ' WIB']);
                fputcsv($handle, []);
                fputcsv($handle, ['RINGKASAN KINERJA KASIR']);
                fputcsv($handle, ['Total Omset Bersih (Rp)', $totalRevenue]);
                fputcsv($handle, ['Total Pesanan Berhasil', $ordersCount . ' Pesanan']);
                fputcsv($handle, ['Total Porsi Masakan Laku', $portionsCount . ' Porsi']);
                fputcsv($handle, ['Rata-rata Nilai Nota (Rp)', $avgTicket]);
                fputcsv($handle, []);
                fputcsv($handle, [
                    'No',
                    'Waktu Transaksi',
                    'Kode Pesanan',
                    'Nomor Antrean',
                    'Nama Pemesan',
                    'Menu Dipesan',
                    'Jumlah Item',
                    'Metode Pembayaran',
                    'Total Bersih (Rp)',
                    'Status',
                ]);

                foreach ($completedOrders as $index => $order) {
                    $menuNames = $order->items->pluck('menu_name_snapshot')->join(', ');
                    $itemCount = $order->items->sum('quantity');
                    $queueNum = $order->queue_number ?? ('#' . str_pad($order->id % 100, 2, '0', STR_PAD_LEFT));

                    fputcsv($handle, [
                        $index + 1,
                        $order->created_at ? $order->created_at->format('d/m/Y H:i') . ' WIB' : '-',
                        $order->order_number,
                        $queueNum,
                        $order->customer_name,
                        $menuNames,
                        $itemCount . ' Porsi',
                        strtoupper($order->payment_method),
                        (int) $order->total,
                        'Lunas',
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['TOTAL PENERIMAAN BERSIH KASIR', '', '', '', '', '', $portionsCount . ' Porsi', '', $totalRevenue, '100% Lunas']);
                fclose($handle);
            }, 200, $headers);
        }

        return view('seller.reports', [
            'seller' => $seller,
            'period' => $period,
            'totalRevenue' => $totalRevenue,
            'omsetTotal' => $totalRevenue,
            'ordersCount' => $ordersCount,
            'completedOrders' => $ordersCount,
            'portionsCount' => $portionsCount,
            'portionsSold' => $portionsCount,
            'avgTicket' => $avgTicket,
            'avgOrderValue' => $avgTicket,
            'topMenus' => $topMenus,
            'recentTransactions' => $recentTransactions,
            'recentOrders' => $recentTransactions,
        ]);
    }

    /**
     * F-23: Modul Prediksi Kebutuhan Porsi AI (Terintegrasi dengan Python FastAPI + Fallback MySQL)
     */
    public function forecastIndex(Request $request, NesaCourtAiService $aiService): View
    {
        $seller = $this->getSeller();

        // Default besok (jika besok Minggu / libur, default ke Senin agar langsung menampilkan hari aktif)
        $defaultDate = now()->addDay();
        if ($defaultDate->isSunday()) {
            $defaultDate = $defaultDate->addDay();
        }

        $targetDateInput = (string) $request->query('date', $defaultDate->format('Y-m-d'));
        try {
            $targetCarbon = Carbon::createFromFormat('Y-m-d', $targetDateInput);
        } catch (\Throwable) {
            $targetCarbon = $defaultDate;
        }
        $targetDateStr = $targetCarbon->format('Y-m-d');

        $weather = $request->query('weather') === 'hujan' ? 'hujan' : 'cerah';
        $hasEvent = $request->boolean('event', false);

        $menus = Menu::with('category')
            ->where('seller_id', $seller->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        // Ambil penjualan riil hari yang sama 7 hari lalu (hanya transaksi 'selesai', bukan 'dibatalkan')
        $sameDayLastWeek = $targetCarbon->copy()->subDays(7)->format('Y-m-d');
        $lastWeekSales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.seller_id', $seller->id)
            ->where('orders.order_status', 'selesai')
            ->whereDate('orders.created_at', $sameDayLastWeek)
            ->selectRaw('order_items.menu_id, SUM(order_items.quantity) as qty')
            ->groupBy('order_items.menu_id')
            ->pluck('qty', 'menu_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        // Ambil rata-rata 7 hari operasional terakhir dari MySQL untuk pembanding / fallback
        $recentDailyRows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.seller_id', $seller->id)
            ->where('orders.order_status', 'selesai')
            ->where('orders.created_at', '>=', now()->subDays(14))
            ->selectRaw('order_items.menu_id, DATE(orders.created_at) as dt, SUM(order_items.quantity) as qty')
            ->groupBy('order_items.menu_id', DB::raw('DATE(orders.created_at)'))
            ->orderBy('dt')
            ->get()
            ->groupBy('menu_id');

        // Panggil Python AI FastAPI (Forecast 1 tenant + metrik evaluasi model)
        $aiSellerResp = $aiService->forecastSeller($seller->id, $targetDateStr, $weather, $hasEvent);
        $aiEval = $aiService->evaluation();
        $aiOnline = $aiSellerResp !== null && ($aiSellerResp['status'] ?? '') === 'ok';

        $aiByMenu = [];
        if ($aiOnline) {
            foreach (($aiSellerResp['forecasts'] ?? []) as $row) {
                if (isset($row['menu_id'])) {
                    $aiByMenu[(int) $row['menu_id']] = $row;
                }
            }
        }

        $forecasts = [];
        $totalPredictedFood = 0;
        $totalPredictedDrink = 0;

        foreach ($menus as $m) {
            $catSlug = $m->category->slug ?? 'makanan';
            $isDrink = in_array($catSlug, ['minuman', 'kopi'], true);
            $itemType = $isDrink ? 'minuman' : 'makanan';

            $menuHistory = $recentDailyRows->get($m->id, collect());
            $avg7 = $menuHistory->isNotEmpty()
                ? (int) round($menuHistory->take(-7)->avg('qty'))
                : max(1, (int) round(($m->stock ?: 15) * 0.8));

            $lastWeekQty = $lastWeekSales[$m->id] ?? $avg7;
            $aiRow = $aiByMenu[$m->id] ?? null;

            // Jika AI online tapi menu ini baru ditambahkan (belum ada di model), panggil /api/ai/forecast/daily dengan custom_history
            if ($aiOnline && $aiRow === null) {
                $customHistory = $menuHistory->map(fn ($r) => [
                    'date' => (string) $r->dt,
                    'quantity' => (int) $r->qty,
                    'order_status' => 'selesai',
                ])->values()->all();

                $aiRow = $aiService->forecastDaily([
                    'menu_id' => $m->id,
                    'seller_id' => $seller->id,
                    'target_date' => $targetDateStr,
                    'weather' => $weather,
                    'has_campus_event' => $hasEvent,
                    'item_type' => $itemType,
                    'price_idr' => (int) $m->price,
                    'daily_stock_quota' => max(1, (int) ($m->stock ?: 15)),
                    'custom_history' => $customHistory,
                ]);
            }

            if ($aiRow !== null) {
                $recommended = (int) ($aiRow['predicted_quantity'] ?? $avg7);
                $rangeMin = (int) ($aiRow['range_min'] ?? max(0, $recommended - 2));
                $rangeMax = (int) ($aiRow['range_max'] ?? $recommended + 2);
                $confidenceRaw = strtolower((string) ($aiRow['confidence_level'] ?? 'sedang'));
                $dataStatus = (string) ($aiRow['data_status'] ?? 'sufficient');
                $isTemporary = (bool) ($aiRow['is_temporary_estimate'] ?? false);
                $keyFactor = (string) ($aiRow['explanation'] ?? 'Prediksi berbasis riwayat penjualan selesai.');
                $modelVersion = (string) ($aiRow['model_version'] ?? 'random_forest-daily-v1');
                $inputPoints = (int) ($aiRow['input_data_points'] ?? $menuHistory->count());

                // Simpan log prediksi harian ke tabel prediction_logs (hindari duplikat di tanggal sama)
                try {
                    PredictionLog::updateOrCreate(
                        [
                            'seller_id' => $seller->id,
                            'menu_id' => $m->id,
                            'prediction_date' => $targetDateStr,
                            'period_type' => 'daily',
                        ],
                        [
                            'predicted_quantity' => $recommended,
                            'model_version' => Str::limit($modelVersion, 50, ''),
                            'input_data_points' => $inputPoints,
                        ]
                    );
                } catch (\Throwable) {
                    // Jangan gagalkan halaman bila logging bermasalah
                }
            } else {
                // Fallback lokal: rata-rata 7 hari operasional dari MySQL (bukan angka hardcode +20%)
                $recommended = $targetCarbon->isSunday() ? 0 : max(1, $avg7);
                $rangeMin = max(0, $recommended - 2);
                $rangeMax = $recommended + 2;
                $confidenceRaw = $menuHistory->count() >= 7 ? 'sedang' : 'rendah';
                $dataStatus = $menuHistory->count() >= 7 ? 'local_fallback' : 'insufficient_data';
                $isTemporary = true;
                $keyFactor = $targetCarbon->isSunday()
                    ? 'Hari Minggu operasional foodcourt tutup/libur.'
                    : 'Fallback lokal rata-rata 7 hari terakhir (nyalakan server Python AI untuk prediksi ML penuh).';
                $modelVersion = 'mysql-ma7-fallback';
                $inputPoints = $menuHistory->count();
            }

            $diffPct = $lastWeekQty > 0 ? (int) round((($recommended - $lastWeekQty) / $lastWeekQty) * 100) : 0;
            $trendStr = ($diffPct >= 0 ? '+' : '') . $diffPct . '%';

            $confidenceLabel = match ($confidenceRaw) {
                'tinggi' => 'Tinggi',
                'rendah' => 'Rendah (Sementara)',
                default => 'Sedang',
            };

            if ($isDrink) {
                $totalPredictedDrink += $recommended;
            } else {
                $totalPredictedFood += $recommended;
            }

            $forecasts[] = [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category->name ?? 'Makanan Utama',
                'itemType' => $itemType,
                'image' => $m->image_url,
                'currentStock' => (int) ($m->stock ?? 0),
                'lastWeekPortions' => $lastWeekQty,
                'recommendedPortions' => $recommended,
                'rangeMin' => $rangeMin,
                'rangeMax' => $rangeMax,
                'trendPercent' => $trendStr,
                'trendPositive' => $diffPct >= 0,
                'confidence' => $confidenceLabel,
                'confidenceLevel' => $confidenceRaw,
                'dataStatus' => $dataStatus,
                'isTemporary' => $isTemporary,
                'keyFactor' => $keyFactor,
                'modelVersion' => $modelVersion,
                'inputDataPoints' => $inputPoints,
            ];
        }

        // Ambil metrik evaluasi jujur dari model AI terlatih
        $dailyEval = $aiEval['daily_forecast'] ?? null;
        $selectedModel = $dailyEval['selected_model'] ?? 'random_forest';
        $bestMetrics = $dailyEval['model_comparison'][$selectedModel] ?? null;
        $wapePct = $bestMetrics['wape_pct'] ?? null;
        $maeVal = $bestMetrics['mae'] ?? null;
        $operatingDays = $dailyEval['split']['total_operating_days'] ?? 77;

        $aiMeta = [
            'online' => $aiOnline,
            'modelName' => $aiOnline ? $selectedModel : 'mysql-ma7-fallback',
            'accuracyLabel' => $wapePct !== null
                ? number_format(max(0, 100 - (float) $wapePct), 1) . '% (MAE ' . $maeVal . ' porsi)'
                : 'Mode Fallback MA-7',
            'operatingDaysLabel' => $aiOnline ? ($operatingDays . ' Hari Operasional') : 'Riwayat 14 Hari MySQL',
            'factorsLabel' => ($weather === 'hujan' ? 'Hujan' : 'Cerah')
                . ($hasEvent ? ' + Acara Kampus' : ' + Pola Kuliah'),
        ];

        $summary = [
            'totalMenus' => count($forecasts),
            'totalFood' => $totalPredictedFood,
            'totalDrink' => $totalPredictedDrink,
            'targetDateIso' => $targetDateStr,
            'targetDateHuman' => $targetCarbon->locale('id')->translatedFormat('l, d F Y'),
            'targetDayName' => $targetCarbon->locale('id')->translatedFormat('l'),
            'isSunday' => $targetCarbon->isSunday(),
        ];

        return view('seller.forecast', [
            'seller' => $seller,
            'forecasts' => $forecasts,
            'summary' => $summary,
            'aiMeta' => $aiMeta,
            'filters' => [
                'date' => $targetDateStr,
                'weather' => $weather,
                'event' => $hasEvent,
            ],
        ]);
    }

    /**
     * F-23 AJAX Endpoint: Prediksi per jam (08:00-16:00) untuk 1 menu milik penjual.
     */
    public function forecastHourly(Request $request, int $menu_id, NesaCourtAiService $aiService): JsonResponse
    {
        $seller = $this->getSeller();
        $menu = Menu::where('id', $menu_id)->where('seller_id', $seller->id)->first();
        if (!$menu) {
            return response()->json(['status' => 'not_found', 'message' => 'Menu tidak ditemukan pada stan ini.'], 404);
        }

        $targetDate = (string) $request->query('date', now()->addDay()->format('Y-m-d'));
        $weather = $request->query('weather') === 'hujan' ? 'hujan' : 'cerah';
        $hasEvent = $request->boolean('event', false);

        $resp = $aiService->forecastHourly($menu->id, $targetDate, $weather, $hasEvent);
        if ($resp === null) {
            return response()->json([
                'status' => 'offline',
                'message' => 'Layanan AI Python (port 8001) sedang tidak aktif sehingga pola prediksi per jam belum dapat dimuat.',
                'hourly_predictions' => [],
            ]);
        }

        return response()->json($resp);
    }

    /**
     * F-24: Profil Stan & Jam Operasional
     */
    public function profileIndex(): View
    {
        $seller = $this->getSeller();

        return view('seller.profile', [
            'seller' => $seller,
            'user' => Auth::user(),
        ]);
    }

    public function profileUpdate(Request $request): RedirectResponse
    {
        $seller = $this->getSeller();

        $validated = $request->validate([
            'stall_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'location' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:25'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $seller->update([
            'stall_name' => $validated['stall_name'],
            'description' => $validated['description'] ?? '',
            'location' => $validated['location'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('stalls', 'public');
            $seller->update(['logo_path' => Storage::url($path)]);
        }

        Auth::user()->update([
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('seller.profile')->with('profile_success', 'Profil stan berhasil diperbarui.');
    }
}
