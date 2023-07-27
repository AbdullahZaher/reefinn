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
        Schema::table('apartments', function (Blueprint $table) {
            $table->renameColumn('current_guest_id', 'current_reservation_id');
            $table->foreign('current_reservation_id')->nullable()->references('id')->on('reservations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropForeign('apartments_current_reservation_id_foreign');
            $table->renameColumn('current_reservation_id', 'current_guest_id');
            $table->foreign('current_guest_id')->nullable()->references('id')->on('reservations');
        });
    }
};
