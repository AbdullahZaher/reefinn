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
        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign('records_guest_id_foreign');
            $table->renameColumn('guest_id', 'reservation_id');
            $table->foreign('reservation_id')->nullable()->references('id')->on('reservations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign('records_reservation_id_foreign');
            $table->renameColumn('reservation_id', 'guest_id');
            $table->foreign('guest_id')->nullable()->references('id')->on('reservations');
        });
    }
};
