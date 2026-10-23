<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel order_status_logs sesuai ERD_NESACOURT.png.
     */
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('actor_user_id')->nullable()->constrained('users');
            $table->enum('actor_type', ['system', 'seller', 'admin', 'buyer']);
            $table->string('old_order_status', 30);
            $table->string('new_order_status', 30);
            $table->string('old_payment_status', 30);
            $table->string('new_payment_status', 30);
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
