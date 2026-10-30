<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\RecommendationLog;
use App\Models\SellerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BuyerController extends Controller
{
    /**
     * F-05: Dashboard Pembeli
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        $activeOrder = Order::with(['seller', 'items'])
            ->where('buyer_id', $user->id)
            ->whereIn('order_status', ['dibuat', 'diterima', 'diproses', 'siap'])
            ->latest()
            ->first();

        $popularMenus = Menu::with('seller')
            ->where('is_active', true)
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->take(6)
            ->get()
            ->map(fn (Menu $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'price' => (int) $m->price,
                'stall' => $m->seller->stall_name ?? 'Stan Kantin',
                'sellerName' => $m->seller->stall_name ?? 'Stan Kantin',
                'prepTime' => 'Masak ~'.($m->estimated_time_minutes ?? 5).' menit',
                'image' => $m->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'desc' => $m->description ?? '',
            ])
            ->values();

        $activeOrderData = null;
        if ($activeOrder) {
            $progressMap = [
                'dibuat' => 15,
                'diterima' => 35,
                'diproses' => 65,
                'siap' => 90,
            ];
            $statusLabels = [
                'dibuat' => 'Pesanan Dibuat',
                'diterima' => 'Pesanan Diterima Penjual',
                'diproses' => 'Sedang Dimasak di Dapur',
                'siap' => 'Pesanan Siap Diambil di Counter',
            ];

            $activeOrderData = [
                'id' => $activeOrder->order_number,
                'stall' => $activeOrder->seller->stall_name ?? 'Stan Kantin',
                'status' => $statusLabels[$activeOrder->order_status] ?? 'Sedang Diproses',
                'statusDesc' => 'Pesanan sedang diproses oleh pihak stan',
                'progress' => $progressMap[$activeOrder->order_status] ?? 50,
                'eta' => '~7 Menit',
                'image' => $activeOrder->items->first()?->menu?->image_path ?: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=150&q=80',
            ];
        }

        return view('buyer.dashboard', [
            'activeOrder' => $activeOrderData,
            'popularMenus' => $popularMenus,
        ]);
    }

    /**
     * F-06 & F-07: Katalog Menu & Stan + Quick Detail Modal
     */
    public function catalog(Request $request): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->slug,
                'label' => $c->name,
            ])
            ->values();

        // Tambah opsi "semua" di depan
        $categories->prepend(['id' => 'semua', 'label' => 'Semua Menu']);

        $stalls = SellerProfile::where('is_active', true)->get();

        $menus = Menu::with(['seller', 'category'])
            ->where('is_active', true)
            ->get()
            ->map(fn (Menu $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category->slug ?? 'makanan',
                'seller' => $m->seller->stall_name ?? 'Stan Kantin',
                'sellerKey' => $m->seller->slug ?? 'stan',
                'sellerId' => $m->seller_id,
                'price' => (int) $m->price,
                'stock' => (int) ($m->stock ?? 0),
                'prepTime' => ($m->estimated_time_minutes ?? 5).' Menit',
                'image' => $m->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'description' => $m->description ?? '',
                'tags' => is_array($m->taste_tags) ? $m->taste_tags : ['Lezat', 'Halal'],
            ])
            ->values();

        return view('buyer.catalog', [
            'categories' => $categories,
            'stalls' => $stalls,
            'menus' => $menus,
            'initialSearch' => $request->query('q', ''),
            'initialCategory' => $request->query('kategori', 'semua'),
        ]);
    }

    /**
     * F-08: Rekomendasi Menu AI
     */
    public function recommendation(Request $request): View
    {
        $menus = Menu::with('seller')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get()
            ->map(function (Menu $m) {
                $tags = is_array($m->taste_tags) ? array_map('strtolower', $m->taste_tags) : [];
                $taste = 'gurih';
                foreach (['pedas', 'manis', 'berkuah', 'segar', 'gurih'] as $t) {
                    if (in_array($t, $tags, true)) {
                        $taste = $t;
                        break;
                    }
                }

                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'price' => (int) $m->price,
                    'seller' => $m->seller->stall_name ?? 'Stan Kantin',
                    'prepTime' => (int) ($m->estimated_time_minutes ?? 5),
                    'taste' => $taste,
                    'category' => $m->category->slug ?? 'makanan',
                    'stock' => (int) ($m->stock ?? 0),
                    'image' => $m->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                    'aiReason' => $m->description ?: 'Menu porsi pas, lezat, dan cepat disajikan di kantin UNESA.',
                ];
            })
            ->values();

        return view('buyer.recommendation', [
            'menus' => $menus,
        ]);
    }

    /**
     * F-09: Keranjang Belanja
     */
    public function cart(): View
    {
        return view('buyer.cart');
    }

    /**
     * F-10: Konfirmasi Checkout
     */
    public function checkout(): View
    {
        $user = Auth::user();

        return view('buyer.checkout', [
            'user' => $user,
        ]);
    }

    /**
     * Proses Checkout / Order Creation (Atomic)
     */
    public function storeOrder(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'service_type' => ['required', 'in:dine_in,takeaway'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'order_notes' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $itemIds = collect($validated['items'])->pluck('id')->all();
        $user = Auth::user();

        try {
            $order = DB::transaction(function () use ($validated, $itemIds, $user) {
                $menus = Menu::with('seller')
                    ->whereIn('id', $itemIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($menus->isEmpty()) {
                    throw new \RuntimeException('Menu yang dipilih tidak ditemukan dalam database.');
                }

                // Validasi Single-Seller
                $sellerIds = $menus->pluck('seller_id')->unique();
                if ($sellerIds->count() > 1) {
                    throw new \RuntimeException('Satu pesanan hanya boleh memuat menu dari satu stan.');
                }
                $sellerId = $sellerIds->first();

                $subtotal = 0;
                $orderItemsData = [];

                foreach ($validated['items'] as $itemInput) {
                    $menu = $menus->get($itemInput['id']);
                    if (! $menu) {
                        continue;
                    }

                    $qty = (int) $itemInput['qty'];

                    // Cek & kurangi stok
                    if ($menu->stock !== null) {
                        if ($menu->stock < $qty) {
                            throw new \RuntimeException("Stok untuk menu '{$menu->name}' tidak mencukupi (sisa: {$menu->stock} porsi).");
                        }
                        $menu->decrement('stock', $qty);
                        if ($menu->stock <= 0) {
                            $menu->update(['is_available' => false]);
                        }
                    }

                    $itemSubtotal = $menu->price * $qty;
                    $subtotal += $itemSubtotal;

                    $orderItemsData[] = [
                        'menu_id' => $menu->id,
                        'menu_name_snapshot' => $menu->name,
                        'menu_price_snapshot' => $menu->price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'note' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $orderNumber = 'NC-'.date('Ymd').'-'.strtoupper(Str::random(4));
                $accessToken = hash('sha256', Str::random(40));

                $serviceNote = $validated['service_type'] === 'dine_in'
                    ? '[Makan di Tempat: '.($validated['table_number'] ?: 'Tanpa Meja').'] '
                    : '[Bawa Pulang] ';

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'buyer_id' => $user->id,
                    'access_token' => $accessToken,
                    'seller_id' => $sellerId,
                    'customer_name' => $validated['customer_name'],
                    'order_status' => 'dibuat',
                    'payment_status' => 'menunggu',
                    'subtotal' => $subtotal,
                    'total' => $subtotal, // Biaya layanan Rp 0
                    'payment_deadline' => now()->addMinutes(15),
                    'note' => trim($serviceNote.($validated['order_notes'] ?? '')),
                ]);

                foreach ($orderItemsData as $item) {
                    $item['order_id'] = $order->id;
                    OrderItem::create($item);
                }

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'actor_user_id' => $user->id,
                    'actor_type' => 'buyer',
                    'old_order_status' => 'dibuat',
                    'new_order_status' => 'dibuat',
                    'old_payment_status' => 'menunggu',
                    'new_payment_status' => 'menunggu',
                    'note' => 'Pesanan baru dibuat oleh pembeli',
                    'created_at' => now(),
                ]);

                return $order;
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_number' => $order->order_number,
                    'redirect' => route('buyer.payment', ['order' => $order->order_number]),
                ]);
            }

            return redirect()->route('buyer.payment', ['order' => $order->order_number]);
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['order' => $e->getMessage()]);
        }
    }

    /**
     * F-11: Pembayaran QRIS Statis
     */
    public function payment(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $orderNumber = $request->query('order');

        $order = null;
        if ($orderNumber) {
            $order = Order::with(['seller.qris', 'items'])
                ->where('order_number', $orderNumber)
                ->where('buyer_id', $user->id)
                ->first();
        }

        if (! $order) {
            // Ambil pesanan terakhir yang belum lunas
            $order = Order::with(['seller.qris', 'items'])
                ->where('buyer_id', $user->id)
                ->where('payment_status', 'menunggu')
                ->latest()
                ->first();
        }

        if (! $order) {
            return redirect()->route('buyer.catalog');
        }

        return view('buyer.payment', [
            'order' => $order,
        ]);
    }

    /**
     * Konfirmasi Pembayaran QRIS dari Pembeli
     */
    public function confirmPayment(Request $request, string $order_number): RedirectResponse
    {
        $user = Auth::user();
        $order = Order::where('order_number', $order_number)
            ->where('buyer_id', $user->id)
            ->firstOrFail();

        if ($order->payment_status === 'menunggu') {
            $order->update([
                'payment_status' => 'perlu_pemeriksaan',
            ]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'actor_user_id' => $user->id,
                'actor_type' => 'buyer',
                'old_order_status' => $order->order_status,
                'new_order_status' => $order->order_status,
                'old_payment_status' => 'menunggu',
                'new_payment_status' => 'perlu_pemeriksaan',
                'note' => 'Pembeli telah mengonfirmasi pembayaran QRIS',
                'created_at' => now(),
            ]);
        }

        return redirect()->route('buyer.order-status', ['order' => $order->order_number]);
    }

    /**
     * F-12: Pelacakan Status Real-time
     */
    public function orderStatus(Request $request): View
    {
        $user = Auth::user();
        $orderNumber = $request->query('order');

        $order = null;
        if ($orderNumber) {
            $order = Order::with(['seller.qris', 'items.menu'])
                ->where('order_number', $orderNumber)
                ->where('buyer_id', $user->id)
                ->first();
        }

        if (! $order) {
            $order = Order::with(['seller.qris', 'items.menu'])
                ->where('buyer_id', $user->id)
                ->latest()
                ->first();
        }

        $currentStep = 1;
        if ($order) {
            if ($order->order_status === 'selesai') {
                $currentStep = 6;
            } elseif ($order->order_status === 'siap') {
                $currentStep = 5;
            } elseif ($order->order_status === 'diproses') {
                $currentStep = 4;
            } elseif (in_array($order->payment_status, ['terkonfirmasi', 'perlu_pemeriksaan'], true)) {
                $currentStep = 3;
            } elseif ($order->payment_status === 'menunggu') {
                $currentStep = 2;
            } else {
                $currentStep = 1;
            }
        }

        return view('buyer.order-status', [
            'order' => $order,
            'currentStep' => $currentStep,
        ]);
    }

    /**
     * F-13: Riwayat Transaksi & Struk Digital
     */
    public function history(): View
    {
        $user = Auth::user();

        $orders = Order::with(['seller', 'items'])
            ->where('buyer_id', $user->id)
            ->latest()
            ->get()
            ->map(function (Order $o) {
                $statusCode = match ($o->order_status) {
                    'selesai' => 'selesai',
                    'dibatalkan' => 'dibatalkan',
                    default => 'diproses',
                };

                $statusLabel = match ($o->order_status) {
                    'dibuat' => 'Pesanan Dibuat',
                    'diterima' => 'Diterima Stan',
                    'diproses' => 'Sedang Dimasak di Dapur',
                    'siap' => 'Siap Diambil di Counter',
                    'selesai' => 'Pesanan Selesai',
                    'dibatalkan' => 'Pesanan Dibatalkan',
                    default => ucfirst($o->order_status),
                };

                $serviceType = str_contains($o->note ?? '', 'Bawa Pulang') ? 'Bawa Pulang (Takeaway)' : 'Makan di Tempat';
                $table = '-';
                if (preg_match('/Makan di Tempat: ([^\]]+)/', $o->note ?? '', $matches)) {
                    $table = $matches[1];
                }

                return [
                    'id' => $o->order_number,
                    'date' => $o->created_at->translatedFormat('d M Y, H:i').' WIB',
                    'stall' => $o->seller->stall_name ?? 'Stan Kantin',
                    'stallImage' => $o->seller->logo_path ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                    'status' => $statusLabel,
                    'statusCode' => $statusCode,
                    'total' => (int) $o->total,
                    'table' => $table,
                    'type' => $serviceType,
                    'paymentMethod' => 'QRIS Statis Stan ('.ucfirst($o->payment_status).')',
                    'items' => $o->items->map(fn (OrderItem $it) => [
                        'name' => $it->menu_name_snapshot,
                        'qty' => $it->quantity,
                        'price' => (int) $it->menu_price_snapshot,
                    ])->values()->all(),
                ];
            })
            ->values();

        return view('buyer.history', [
            'orders' => $orders,
        ]);
    }

    /**
     * F-14: Profil Akun Pembeli
     */
    public function profile(): View
    {
        $user = Auth::user();

        $totalCompleted = Order::where('buyer_id', $user->id)
            ->where('order_status', 'selesai')
            ->count();

        $favStall = Order::where('buyer_id', $user->id)
            ->select('seller_id', DB::raw('count(*) as count'))
            ->groupBy('seller_id')
            ->orderByDesc('count')
            ->with('seller')
            ->first()?->seller?->stall_name ?? 'Stan 01 - Barokah Rasa';

        return view('buyer.profile', [
            'user' => $user,
            'totalCompleted' => $totalCompleted,
            'favStall' => $favStall,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25'],
        ]);

        $user->update($validated);

        return back()->with('profile_success', 'Data profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('password_success', 'Kata sandi akun Anda berhasil diganti.');
    }
}
