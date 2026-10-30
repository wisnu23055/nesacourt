<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class BuyerTest extends TestCase
{
    public function test_buyer_flow(): void
    {
        $buyer = User::where('role', 'buyer')->first();
        $this->assertNotNull($buyer, 'Buyer user must exist in DB');

        // 1. Katalog & Rekomendasi
        $this->actingAs($buyer)->get('/katalog')->assertStatus(200);
        $this->actingAs($buyer)->get('/rekomendasi')->assertStatus(200);

        // 2. Checkout Order
        $menu = Menu::where('is_active', true)->where('stock', '>=', 2)->first();
        $this->assertNotNull($menu, 'Menu with stock >= 2 must exist');
        $initialStock = $menu->stock;

        $response = $this->actingAs($buyer)->postJson('/checkout', [
            'service_type' => 'dine_in',
            'table_number' => 'Meja 09',
            'customer_name' => $buyer->name,
            'customer_phone' => $buyer->phone,
            'order_notes' => 'Tidak pakai seledri',
            'items' => [
                ['id' => $menu->id, 'qty' => 2],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);

        // Verifikasi stok berkurang
        $menu->refresh();
        $this->assertEquals($initialStock - 2, $menu->stock);

        // Verifikasi Order & Item di DB
        $order = Order::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);
        $this->assertEquals('dibuat', $order->order_status);
        $this->assertEquals('menunggu', $order->payment_status);
        $this->assertCount(1, $order->items);

        // 3. Konfirmasi Bayar
        $this->actingAs($buyer)
            ->post("/pesanan/{$orderNumber}/konfirmasi-bayar")
            ->assertRedirect(route('buyer.order-status', ['order' => $orderNumber]));

        $order->refresh();
        $this->assertEquals('perlu_pemeriksaan', $order->payment_status);

        // 4. Status, Riwayat, Profil
        $this->actingAs($buyer)->get("/pesanan/status?order={$orderNumber}")->assertStatus(200);
        $this->actingAs($buyer)->get('/riwayat')->assertStatus(200);
        $this->actingAs($buyer)->get('/profil')->assertStatus(200);
    }

    // ponytail: test pembatalan dan rollback kuantitas stok menu (Blueprint B-15)
    public function test_buyer_order_cancel_restores_stock(): void
    {
        $buyer = User::where('role', 'buyer')->first();
        $menu = Menu::where('is_active', true)->where('stock', '>=', 2)->first();
        $initialStock = $menu->stock;

        // 1. Checkout 1 item
        $response = $this->actingAs($buyer)->postJson('/checkout', [
            'service_type' => 'dine_in',
            'table_number' => 'Meja 12',
            'customer_name' => $buyer->name,
            'customer_phone' => $buyer->phone,
            'items' => [
                ['id' => $menu->id, 'qty' => 1],
            ],
        ]);
        $orderNumber = $response->json('order_number');
        $this->assertEquals($initialStock - 1, $menu->fresh()->stock);

        // 2. Batalkan pesanan
        $cancelResponse = $this->actingAs($buyer)->post("/pesanan/{$orderNumber}/batal");
        $cancelResponse->assertRedirect(route('buyer.history'));

        // 3. Verifikasi stok porsi kembali utuh
        $this->assertEquals($initialStock, $menu->fresh()->stock);

        $order = Order::where('order_number', $orderNumber)->first();
        $this->assertEquals('dibatalkan', $order->order_status);
        $this->assertEquals('dibatalkan', $order->payment_status);

        // 4. Verifikasi proteksi rollback ganda
        $this->assertFalse($order->cancelAndRestoreStock());
        $this->assertEquals($initialStock, $menu->fresh()->stock);
    }
}
