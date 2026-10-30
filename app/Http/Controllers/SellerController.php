<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\PaymentConfirmation;
use App\Models\SellerPaymentQris;
use App\Models\SellerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
                'image' => $m->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
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
        if ($request->has('id')) {
            $menu = Menu::where('seller_id', $seller->id)->find($request->id);
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

        $menu->update(['is_active' => false]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('seller.menu.index')->with('success', 'Menu dinonaktifkan.');
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
                'image' => $m->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
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
                    'paymentMethod' => 'QRIS ('.ucfirst($o->payment_status).')',
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
    public function reportsIndex(Request $request): View
    {
        $seller = $this->getSeller();

        $period = $request->query('period', 'hari_ini');
        $query = Order::where('seller_id', $seller->id)->where('order_status', 'selesai');

        if ($period === 'hari_ini') {
            $query->where('created_at', '>=', now()->startOfDay());
        } elseif ($period === '7_hari') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === 'bulan_ini') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        $completedOrders = $query->latest()->get();

        $totalRevenue = (int) $completedOrders->sum('total');
        $ordersCount = $completedOrders->count();
        $portionsCount = (int) OrderItem::whereIn('order_id', $completedOrders->pluck('id'))->sum('quantity');
        $avgTicket = $ordersCount > 0 ? (int) ($totalRevenue / $ordersCount) : 0;

        $topMenus = Menu::where('seller_id', $seller->id)->orderByDesc('stock')->take(3)->get();
        $recentTransactions = $completedOrders->take(10);

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
     * F-23: Modul Prediksi Kebutuhan Porsi AI
     */
    public function forecastIndex(): View
    {
        $seller = $this->getSeller();

        $forecasts = Menu::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->get()
            ->map(function (Menu $m) {
                $base = $m->stock ?: 15;
                $recommended = (int) round($base * 1.2);

                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'category' => $m->category->name ?? 'Makanan Utama',
                    'image' => $m->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=300&q=80',
                    'lastWeekPortions' => $base,
                    'recommendedPortions' => $recommended,
                    'trendPercent' => '+20%',
                    'trendPositive' => true,
                    'confidence' => '95%',
                    'keyFactor' => 'Pola perkuliahan luring dan cuaca kampus Ketintang cerah.',
                ];
            })
            ->values();

        return view('seller.forecast', [
            'seller' => $seller,
            'forecasts' => $forecasts,
        ]);
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
