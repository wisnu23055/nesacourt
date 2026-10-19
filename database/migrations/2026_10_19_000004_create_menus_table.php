<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel menus sesuai ERD_NESACOURT.png.
     * ponytail: ERD tidak menyebut DEFAULT; enum/boolean NOT NULL diberi
     * default agar insert aman. Hapus kalau mau patuh 100% ERD.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('name', 100);
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->integer('estimated_time_minutes')->nullable();
            $table->json('taste_tags')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
