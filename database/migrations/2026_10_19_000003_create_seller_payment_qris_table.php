<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel seller_payment_qris sesuai ERD_NESACOURT.png.
     * ponytail: ERD tidak menyebut DEFAULT; enum/boolean NOT NULL diberi
     * default agar insert aman. Hapus kalau mau patuh 100% ERD.
     */
    public function up(): void
    {
        Schema::create('seller_payment_qris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->unique()->constrained('seller_profiles');
            $table->string('image_path', 255);
            $table->string('merchant_name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_payment_qris');
    }
};
