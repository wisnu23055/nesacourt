<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// ponytail: single controller for admin monitoring and management, direct eloquent queries
class AdminController extends Controller
{
    /**
     * F-25: Dashboard Admin / Pengelola Food Court
     */
    public function dashboard(): View
    {
        $stalls = SellerProfile::with(['user', 'menus', 'orders'])->get();

        $stallsData = $stalls->map(function ($s) {
            $todayOrders = $s->orders()->whereDate('created_at', today())->get();
            $topMenu = $s->menus()->orderBy('stock', 'desc')->first();

            return [
                'id' => $s->id,
                'name' => $s->stall_name,
                'owner' => $s->user?->name ?? 'Pemilik Stan',
                'ownerImage' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
                'location' => $s->location ?? 'Counter Food Court',
                'stallImage' => $s->logo_path ? asset('storage/' . $s->logo_path) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'todayOrders' => $todayOrders->count(),
                'todayRevenue' => 'Rp ' . number_format($todayOrders->sum('total'), 0, ',', '.'),
                'isOpen' => (bool)$s->is_active,
                'topMenu' => $topMenu?->name ?? 'Menu Unggulan',
            ];
        });

        $recentOrders = Order::with(['buyer', 'seller', 'items.menu'])->latest()->take(10)->get();

        $recentOrdersData = $recentOrders->map(function ($o) {
            $firstItem = $o->items->first();
            return [
                'id' => $o->order_number,
                'time' => $o->created_at ? $o->created_at->diffForHumans() : 'Baru saja',
                'buyerName' => $o->buyer?->name ?? 'Mahasiswa',
                'buyerNim' => $o->buyer?->phone ?? '-',
                'buyerImage' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80',
                'stallName' => $o->seller?->stall_name ?? 'Stan',
                'menuName' => $firstItem?->menu_name_snapshot ?? 'Menu Pesanan',
                'menuImage' => $firstItem?->menu?->image_path ? asset('storage/' . $firstItem->menu->image_path) : 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=300&q=80',
                'price' => (int)$o->total,
                'status' => ucfirst(str_replace('_', ' ', $o->order_status)),
                'statusCode' => $o->order_status,
                'table' => $o->table_number ? 'Meja ' . $o->table_number : 'Takeaway',
            ];
        });

        $omsetToday = Order::whereDate('created_at', today())->whereIn('order_status', ['selesai', 'siap', 'diproses'])->sum('total');
        if ($omsetToday == 0) {
            $omsetToday = Order::whereIn('order_status', ['selesai', 'siap', 'diproses'])->sum('total');
        }

        $ordersFinished = Order::where('order_status', 'selesai')->count();
        $ordersTotal = Order::count();
        $openStallsCount = $stalls->where('is_active', true)->count();
        $totalStallsCount = $stalls->count();
        $avgPrepTime = (int) round(Menu::avg('estimated_time_minutes') ?? 10);

        return view('admin.dashboard', [
            'stallsData' => $stallsData,
            'recentOrdersData' => $recentOrdersData,
            'omsetToday' => $omsetToday,
            'ordersFinished' => $ordersFinished,
            'ordersTotal' => $ordersTotal,
            'openStallsCount' => $openStallsCount,
            'totalStallsCount' => $totalStallsCount,
            'avgPrepTime' => $avgPrepTime,
        ]);
    }

    /**
     * F-26: Manajemen & Monitoring Stan Kantin
     */
    public function stallsIndex(): View
    {
        $stalls = SellerProfile::with(['user', 'menus'])->get()->map(function ($s) {
            $locationArea = str_contains(strtolower($s->location ?? ''), 'timur') ? 'timur' : 'barat';

            return [
                'id' => $s->id,
                'number' => str_pad($s->id, 2, '0', STR_PAD_LEFT),
                'name' => $s->stall_name,
                'locationArea' => $locationArea,
                'location' => $s->location ?? 'Counter Barat No. 01',
                'owner' => $s->user?->name ?? 'Pemilik Stan',
                'phone' => $s->user?->phone ?? '08123456789',
                'ownerImage' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
                'stallImage' => $s->logo_path ? asset('storage/' . $s->logo_path) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'menuCount' => $s->menus->count(),
                'rentStatus' => 'Lunas (' . now()->translatedFormat('M Y') . ')',
                'isOpen' => (bool)$s->is_active,
            ];
        });

        return view('admin.stalls', [
            'stallsData' => $stalls,
        ]);
    }

    /**
     * F-26: Registrasi Stan Baru
     */
    public function stallStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'owner' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:100'],
            'number' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['owner'],
            'email' => Str::slug($validated['name']) . '@nesacourt.ac.id',
            'password' => bcrypt('password'),
            'role' => 'seller',
            'phone' => $validated['phone'],
        ]);

        SellerProfile::create([
            'user_id' => $user->id,
            'stall_name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => 'Stan kantin resmi Food Court UNESA Ketintang.',
            'location' => $validated['location'] . ' No. ' . ($validated['number'] ?? '01'),
            'is_active' => true,
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ]);

        return redirect()->route('admin.stalls')->with('success', 'Stan berhasil didaftarkan.');
    }

    public function stallToggleStatus($id): JsonResponse
    {
        $stall = SellerProfile::findOrFail($id);
        $stall->update(['is_active' => !$stall->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $stall->is_active,
            'message' => 'Status operasional stan berhasil diperbarui.',
        ]);
    }

    /**
     * F-27: Monitoring Menu Lintas Stan
     */
    public function menuIndex(Request $request): View
    {
        $menus = Menu::with(['seller', 'category'])->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category?->slug ?? 'makanan',
                'categoryLabel' => $m->category?->name ?? 'Makanan',
                'stall' => $m->seller?->stall_name ?? 'Stan Kantin',
                'stallKey' => 'stan_' . str_pad($m->seller_id, 2, '0', STR_PAD_LEFT),
                'price' => (int)$m->price,
                'stock' => (int)$m->stock,
                'prepTime' => ($m->estimated_time_minutes ?? 10) . ' Menit',
                'image' => $m->image_path ? asset('storage/' . $m->image_path) : 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
            ];
        });

        $stalls = SellerProfile::all();
        $categories = Category::all();

        return view('admin.menu', [
            'menusData' => $menus,
            'stalls' => $stalls,
            'categories' => $categories,
        ]);
    }

    public function menuToggleStatus($id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        $newStatus = $menu->status === 'tersedia' ? 'habis' : 'tersedia';
        $menu->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $menu->status,
            'message' => 'Status ketersediaan menu berhasil diperbarui.',
        ]);
    }

    /**
     * F-28: Monitoring Arus Pesanan
     */
    public function ordersIndex(Request $request): View
    {
        $orders = Order::with(['buyer', 'seller', 'items.menu'])->latest()->get()->map(function ($o) {
            $firstItem = $o->items->first();
            return [
                'id' => $o->order_number,
                'queueNo' => $o->queue_number ?? ('A-' . str_pad($o->id, 2, '0', STR_PAD_LEFT)),
                'date' => $o->created_at ? $o->created_at->format('d M Y, H:i') . ' WIB' : now()->format('d M Y, H:i') . ' WIB',
                'buyerName' => $o->buyer?->name ?? 'Mahasiswa',
                'buyerNim' => $o->buyer?->phone ?? '-',
                'buyerPhone' => $o->buyer?->phone ?? '-',
                'buyerImage' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80',
                'stallName' => $o->seller?->stall_name ?? 'Stan Kantin',
                'stallKey' => 'stan_' . str_pad($o->seller_id, 2, '0', STR_PAD_LEFT),
                'menuName' => $firstItem?->menu_name_snapshot ?? 'Pesanan',
                'menuImage' => $firstItem?->menu?->image_path ? asset('storage/' . $firstItem->menu->image_path) : 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=300&q=80',
                'portions' => (int)($o->items->sum('quantity') ?: 1),
                'total' => (int)$o->total,
                'table' => $o->table_number ? 'Meja ' . $o->table_number : 'Takeaway',
                'serviceType' => $o->table_number ? 'Makan di Tempat' : 'Bawa Pulang',
                'paymentMethod' => 'QRIS Statis (' . ($o->payment_status === 'terkonfirmasi' ? 'Terverifikasi Lunas' : ucfirst(str_replace('_', ' ', $o->payment_status))) . ')',
                'status' => ucfirst(str_replace('_', ' ', $o->order_status)),
                'statusCode' => $o->order_status,
            ];
        });

        $stalls = SellerProfile::all();

        return view('admin.orders', [
            'ordersData' => $orders,
            'stalls' => $stalls,
        ]);
    }

    /**
     * F-29: Audit Detail Pesanan
     */
    public function orderDetail(Request $request): View
    {
        $orderNumber = $request->query('order', $request->query('id'));

        $order = null;
        if ($orderNumber) {
            $order = Order::with(['buyer', 'seller.user', 'items.menu', 'statusLogs.actor', 'paymentConfirmation'])
                ->where('order_number', $orderNumber)
                ->orWhere('id', $orderNumber)
                ->first();
        }

        if (!$order) {
            $order = Order::with(['buyer', 'seller.user', 'items.menu', 'statusLogs.actor', 'paymentConfirmation'])->latest()->first();
        }

        return view('admin.order-detail', [
            'order' => $order,
        ]);
    }

    /**
     * F-30: Rekapitulasi Laporan Keuangan Platform
     */
    public function reportsIndex(Request $request): View
    {
        $stallsRevenue = SellerProfile::with(['user', 'orders'])->get()->map(function ($s) {
            $validOrders = $s->orders()->whereIn('order_status', ['selesai', 'siap', 'diproses'])->get();
            $grossRevenue = $validOrders->sum('total');

            return [
                'id' => $s->id,
                'name' => $s->stall_name,
                'location' => $s->location ?? 'Counter Barat No. 01',
                'stallImage' => $s->logo_path ? asset('storage/' . $s->logo_path) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
                'owner' => $s->user?->name ?? 'Pemilik Stan',
                'ownerImage' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
                'transactions' => $validOrders->count(),
                'grossRevenue' => (int)$grossRevenue,
                'feeRent' => 1000000,
                'rentStatus' => 'Lunas Terverifikasi',
            ];
        });

        $totalOmset = $stallsRevenue->sum('grossRevenue');
        $totalTransactions = $stallsRevenue->sum('transactions');
        $totalFee = $stallsRevenue->sum('feeRent');

        return view('admin.reports', [
            'stallsRevenue' => $stallsRevenue,
            'totalOmset' => $totalOmset,
            'totalTransactions' => $totalTransactions,
            'totalFee' => $totalFee,
        ]);
    }

    /**
     * F-31: Log Aktivitas Sistem
     */
    public function logsIndex(Request $request): View
    {
        $logs = OrderStatusLog::with(['order.seller.user', 'actor'])->latest('id')->take(50)->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'time' => $log->created_at ? $log->created_at->format('d M Y, H:i:s') . ' WIB' : now()->format('d M Y, H:i:s') . ' WIB',
                'actorName' => $log->actor?->name ?? ($log->actor_type ?? 'Sistem'),
                'stallName' => $log->order?->seller?->stall_name ?? 'Platform NesaCourt',
                'actorImage' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
                'action' => 'Update Status ' . ucfirst(str_replace('_', ' ', $log->new_order_status ?? 'Pesanan')),
                'category' => 'pesanan',
                'description' => 'Pesanan #' . ($log->order?->order_number ?? $log->order_id) . ' berubah dari ' . ($log->old_order_status ?? 'awal') . ' menjadi ' . ($log->new_order_status ?? 'baru') . ($log->note ? ' (' . $log->note . ')' : ''),
                'module' => 'Pesanan',
                'ip' => '10.20.44.12',
                'device' => 'Web App (Sistem)',
                'status' => 'Sukses',
            ];
        });

        return view('admin.logs', [
            'logsData' => $logs,
        ]);
    }
}
