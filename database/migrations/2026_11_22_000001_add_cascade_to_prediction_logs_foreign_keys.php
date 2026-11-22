<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log prediksi adalah turunan menu & stan. Tanpa cascade, menghapus menu
     * yang pernah diprediksi gagal dengan SQLSTATE 1451.
     */
    public function up(): void
    {
        Schema::table('prediction_logs', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropForeign(['seller_id']);
        });

        Schema::table('prediction_logs', function (Blueprint $table) {
            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->foreign('seller_id')->references('id')->on('seller_profiles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prediction_logs', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropForeign(['seller_id']);
        });

        Schema::table('prediction_logs', function (Blueprint $table) {
            $table->foreign('menu_id')->references('id')->on('menus');
            $table->foreign('seller_id')->references('id')->on('seller_profiles');
        });
    }
};
