<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel prediction_logs sesuai ERD_NESACOURT.png.
     */
    public function up(): void
    {
        Schema::create('prediction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles');
            $table->foreignId('menu_id')->constrained('menus');
            $table->date('prediction_date');
            $table->enum('period_type', ['daily', 'hourly']);
            $table->integer('predicted_quantity');
            $table->integer('actual_quantity')->nullable();
            $table->string('model_version', 50);
            $table->integer('input_data_points');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_logs');
    }
};
