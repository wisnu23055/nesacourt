<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    // ponytail: test halaman error kustom 404, 403, dan komponen bersama
    public function test_404_error_page_renders_properly(): void
    {
        $response = $this->get('/halaman-acak-yang-tidak-pernah-ada-999');
        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_403_error_page_renders_when_unauthorized(): void
    {
        $buyer = User::where('role', 'buyer')->first();
        if (!$buyer) {
            $buyer = User::create([
                'name' => 'Mahasiswa Test Error',
                'email' => 'mhs.error@nesacourt.ac.id',
                'phone' => '08999999991',
                'role' => 'buyer',
                'password' => bcrypt('password123'),
            ]);
        }

        $response = $this->actingAs($buyer)->get('/admin/dashboard');
        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Akses Halaman Dibatasi');
    }

    public function test_flash_messages_component_renders_on_session(): void
    {
        $buyer = User::where('role', 'buyer')->first();
        if (!$buyer) {
            $buyer = User::create([
                'name' => 'Mahasiswa Test Flash',
                'email' => 'mhs.flash@nesacourt.ac.id',
                'phone' => '08999999992',
                'role' => 'buyer',
                'password' => bcrypt('password123'),
            ]);
        }

        $response = $this->actingAs($buyer)
            ->withSession(['success' => 'Pemberitahuan sukses berhasil tampil'])
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Pemberitahuan sukses berhasil tampil');
        $response->assertSee('Berhasil');
    }
}
