<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel orders sesuai ERD_NESACOURT.png.
     * ponytail: ERD tidak menyebut DEFAULT; enum/boolean NOT NULL diberi
     * default agar insert aman. Hapus kalau mau patuh 100% ERD.
     * payment_status lengkap diambil dari tabel status Blueprint_NESACOURT.pdf
     * (ERD menulis "terkonfirmasi, ...").
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('buyer_id')->constrained('users');
            $table->string('access_token', 64)->nullable();
            $table->foreignId('seller_id')->constrained('seller_profiles');
            $table->string('customer_name', 100);
            $table->enum('order_status', [
                'dibuat', 'diterima', 'diproses', 'siap', 'selesai', 'dibatalkan',
            ])->default('dibuat');
            // ERD memuat "menunggu, perlu_pemeriksaan, terkonfirmasi, ..." ->
            // nilai lengkapnya diambil dari tabel status di Blueprint_NESACOURT.
            $table->enum('payment_status', [
                'menunggu', 'perlu_pemeriksaan', 'terkonfirmasi',
                'perlu_pemeriksaan_ulang', 'dibatalkan',
            ])->default('menunggu');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamp('payment_deadline')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
