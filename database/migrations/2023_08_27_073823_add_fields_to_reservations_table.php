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
            $table->tinyInteger('type')->default(1)->after('r_id');
            $table->decimal('taxes_amount', 16, 2)->default(0.0)->after('number_of_nights');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('taxes_percentage', 16, 2)->default(0.0)->after('taxes_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('taxes_amount');
            $table->dropColumn('taxes_percentage');
        });
    }
};