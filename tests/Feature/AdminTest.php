<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\SellerProfile;
use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    // ponytail: test flow admin dasar mencakup F-25 s/d F-31 tanpa mocking berat
    public function test_admin_flow(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Admin Pengelola Kantin',
                'email' => 'admin@nesacourt.ac.id',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => bcrypt('password123'),
            ]);
        }

        $buyerUser = User::where('role', 'buyer')->first();
        if (!$buyerUser) {
            $buyerUser = User::create([
                'name' => 'Mahasiswa Test Admin Auth',
                'email' => 'mhs.adminauth@nesacourt.ac.id',
                'phone' => '08999999993',
                'role' => 'buyer',
                'password' => bcrypt('password123'),
            ]);
        }

        // 1. Authorization check: buyer cannot access admin dashboard
        $response = $this->actingAs($buyerUser)->get('/admin/dashboard');
        $response->assertStatus(403);

        // 2. Admin Dashboard (F-25)
        $response = $this->actingAs($adminUser)->get('/admin/dashboard');
        $response->assertStatus(200);

        // 3. Admin Stalls Management (F-26 & F-27)
        $response = $this->actingAs($adminUser)->get('/admin/stan');
        $response->assertStatus(200);

        $seller = SellerProfile::first();
        if ($seller) {
            $prevStatus = $seller->is_active;
            $toggleResponse = $this->actingAs($adminUser)->postJson("/admin/stan/{$seller->id}/toggle");
            $toggleResponse->assertStatus(200);
            $this->assertDatabaseHas('seller_profiles', [
                'id' => $seller->id,
                'is_active' => !$prevStatus,
            ]);

            // Revert back
            $this->actingAs($adminUser)->postJson("/admin/stan/{$seller->id}/toggle");
        }

        // 4. Admin Menu Monitoring (F-28)
        $response = $this->actingAs($adminUser)->get('/admin/menu');
        $response->assertStatus(200);

        $menu = Menu::first();
        if ($menu) {
            $prevStatus = $menu->status;
            $toggleResponse = $this->actingAs($adminUser)->postJson("/admin/menu/{$menu->id}/toggle");
            $toggleResponse->assertStatus(200);
            // Revert back
            $this->actingAs($adminUser)->postJson("/admin/menu/{$menu->id}/toggle");
        }

        // 5. Admin Orders Monitoring & Detail (F-29)
        $response = $this->actingAs($adminUser)->get('/admin/pesanan');
        $response->assertStatus(200);

        $order = Order::first();
        if ($order) {
            $response = $this->actingAs($adminUser)->get("/admin/pesanan/{$order->id}");
            $response->assertStatus(200);
        }

        // 6. Admin Financial Reports (F-30)
        $response = $this->actingAs($adminUser)->get('/admin/laporan');
        $response->assertStatus(200);

        // 7. Admin Logs Viewer (F-31)
        $response = $this->actingAs($adminUser)->get('/admin/log');
        $response->assertStatus(200);
    }
}
