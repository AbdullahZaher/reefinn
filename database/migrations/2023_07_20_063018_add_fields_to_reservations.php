<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('copy')->default(0.0)->after('guest_id');
            $table->decimal('price_for_night', 16, 2)->default(0.0)->after('real_checkout');
            $table->decimal('total_price', 16, 2)->default(0.0)->after('price_for_night');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('copy');
            $table->dropColumn('price_for_night');
            $table->dropColumn('total_price');
        });
    }
};