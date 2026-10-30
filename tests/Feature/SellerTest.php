<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\PaymentConfirmation;
use App\Models\SellerProfile;
use App\Models\User;
use Tests\TestCase;

class SellerTest extends TestCase
{
    public function test_seller_flow(): void
    {
        $sellerUser = User::where('email', 'barokah@nesacourt.ac.id')->firstOrFail();
        $seller = $sellerUser->sellerProfile;
        $this->assertNotNull($seller);

        // 1. Dashboard
        $response = $this->actingAs($sellerUser)->get('/penjual/dashboard');
        $response->assertStatus(200);

        // 2. Menu Index & Create
        $response = $this->actingAs($sellerUser)->get('/penjual/menu');
        $response->assertStatus(200);

        $category = Category::first();
        $response = $this->actingAs($sellerUser)->post('/penjual/menu', [
            'name' => 'Menu Uji Penjual ' . time(),
            'category' => $category ? $category->slug : 'makanan',
            'price' => 12500,
            'stock' => 15,
            'estimated_time_minutes' => 8,
            'description' => 'Menu uji coba sistem seller',
            'status' => 'tersedia',
        ]);
        $response->assertRedirect('/penjual/menu');

        $createdMenu = Menu::where('seller_id', $seller->id)
            ->where('name', 'like', 'Menu Uji Penjual%')
            ->first();
        $this->assertNotNull($createdMenu);
        $this->assertEquals(12500, $createdMenu->price);

        // Verify Menu Edit Route
        $response = $this->actingAs($sellerUser)->get('/penjual/menu/' . $createdMenu->id . '/edit');
        $response->assertStatus(200);

        // 3. Stock Update
        $response = $this->actingAs($sellerUser)->postJson('/penjual/stok', [
            'stocks' => [
                ['id' => $createdMenu->id, 'stock' => 33],
            ]
        ]);
        $response->assertStatus(200);
        $this->assertEquals(33, $createdMenu->fresh()->stock);

        // 4. Create an Order & Advance Status
        $buyerUser = User::where('role', 'buyer')->first() ?? $sellerUser;
        $order = Order::create([
            'order_number' => 'NC-TEST-' . strtoupper(substr(uniqid(), -6)),
            'seller_id' => $seller->id,
            'buyer_id' => $buyerUser->id,
            'customer_name' => 'Mahasiswa Test Seller',
            'customer_phone' => '08999999999',
            'dining_option' => 'dine_in',
            'table_number' => '05',
            'total_items' => 1,
            'subtotal' => 12500,
            'tax' => 0,
            'platform_fee' => 0,
            'total' => 12500,
            'payment_method' => 'qris',
            'payment_status' => 'perlu_pemeriksaan',
            'order_status' => 'diterima',
            'access_token' => bin2hex(random_bytes(16)),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $createdMenu->id,
            'menu_name_snapshot' => $createdMenu->name,
            'menu_price_snapshot' => $createdMenu->price,
            'quantity' => 1,
            'subtotal' => 12500,
        ]);

        // Seller advance status to diproses
        $response = $this->actingAs($sellerUser)->postJson('/penjual/pesanan/' . $order->order_number . '/status', [
            'order_status' => 'diproses',
        ]);
        $response->assertStatus(200);
        $this->assertEquals('diproses', $order->fresh()->order_status);

        // Check log created
        $log = OrderStatusLog::where('order_id', $order->id)->where('new_order_status', 'diproses')->first();
        $this->assertNotNull($log);

        // Advance to siap
        $this->actingAs($sellerUser)->postJson('/penjual/pesanan/' . $order->order_number . '/status', [
            'order_status' => 'siap',
        ]);
        $this->assertEquals('siap', $order->fresh()->order_status);

        // Advance to selesai
        $this->actingAs($sellerUser)->postJson('/penjual/pesanan/' . $order->order_number . '/status', [
            'order_status' => 'selesai',
        ]);
        $this->assertEquals('selesai', $order->fresh()->order_status);
        $this->assertEquals('terkonfirmasi', $order->fresh()->payment_status);

        // 5. Orders Detail
        $response = $this->actingAs($sellerUser)->get('/penjual/pesanan/detail?id=' . $order->order_number);
        $response->assertStatus(200);

        // 6. QRIS Update
        $response = $this->actingAs($sellerUser)->post('/penjual/qris', [
            'merchant_name' => 'Stan Barokah Updated',
        ]);
        $response->assertRedirect('/penjual/qris');
        $this->assertNotNull($seller->fresh()->qris);
        $this->assertEquals('Stan Barokah Updated', $seller->fresh()->qris->merchant_name);

        // 7. Reports & Forecast
        $response = $this->actingAs($sellerUser)->get('/penjual/laporan');
        $response->assertStatus(200);

        $response = $this->actingAs($sellerUser)->get('/penjual/prediksi');
        $response->assertStatus(200);

        // 8. Profile Update
        $response = $this->actingAs($sellerUser)->post('/penjual/profil', [
            'stall_name' => 'Stan 01 Barokah Rasa',
            'description' => 'Spesialis Ayam Geprek & Nasi Penyet',
            'location' => 'Counter Barat No. 01',
            'phone' => '081234567890',
        ]);
        $response->assertRedirect('/penjual/profil');
        $this->assertEquals('Counter Barat No. 01', $seller->fresh()->location);

        // Clean up created test menu & order
        PaymentConfirmation::where('order_id', $order->id)->delete();
        $order->items()->delete();
        $order->statusLogs()->delete();
        $order->delete();
        $createdMenu->delete();
    }

    // ponytail: test pembatalan oleh penjual dan rollback stok porsi (Blueprint B-15)
    public function test_seller_cancel_order_restores_stock(): void
    {
        $sellerUser = User::where('email', 'barokah@nesacourt.ac.id')->firstOrFail();
        $seller = $sellerUser->sellerProfile;
        $menu = Menu::where('seller_id', $seller->id)->where('stock', '>=', 2)->firstOrFail();
        $initialStock = $menu->stock;

        $buyerUser = User::where('role', 'buyer')->first() ?? $sellerUser;
        $order = Order::create([
            'order_number' => 'NC-TEST-' . strtoupper(substr(uniqid(), -6)),
            'seller_id' => $seller->id,
            'buyer_id' => $buyerUser->id,
            'customer_name' => 'Test Pembeli Batal Seller',
            'order_status' => 'diterima',
            'payment_status' => 'menunggu',
            'subtotal' => $menu->price,
            'total' => $menu->price,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'menu_name_snapshot' => $menu->name,
            'menu_price_snapshot' => $menu->price,
            'quantity' => 1,
            'subtotal' => $menu->price,
        ]);
        $menu->decrement('stock', 1);
        $this->assertEquals($initialStock - 1, $menu->fresh()->stock);

        // Seller membatalkan pesanan
        $response = $this->actingAs($sellerUser)->postJson('/penjual/pesanan/' . $order->order_number . '/status', [
            'order_status' => 'dibatalkan',
        ]);
        $response->assertStatus(200);

        // Verifikasi stok kembali dan status dibatalkan
        $this->assertEquals($initialStock, $menu->fresh()->stock);
        $this->assertEquals('dibatalkan', $order->fresh()->order_status);
        $this->assertEquals('dibatalkan', $order->fresh()->payment_status);

        // Cleanup
        $order->items()->delete();
        $order->statusLogs()->delete();
        $order->delete();
    }
}
