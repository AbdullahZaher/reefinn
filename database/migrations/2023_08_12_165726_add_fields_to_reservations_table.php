<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        DB::table('reservations')->get()->each(function ($reservation) {
            DB::table('reservations')->where('id', $reservation->id)->update([
                'uuid' => Str::uuid()->toString(),
            ]);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};