<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel recommendation_logs sesuai ERD_NESACOURT.png.
     * Log rekomendasi AI: relasi ke menus berupa JSON, bukan FK.
     */
    public function up(): void
    {
        Schema::create('recommendation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100);
            $table->decimal('budget', 12, 2);
            $table->string('taste_preference', 100);
            $table->integer('max_wait_minutes');
            $table->json('recommended_menu_ids')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_logs');
    }
};
