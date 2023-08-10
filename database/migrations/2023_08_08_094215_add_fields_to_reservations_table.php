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
            $table->integer('id_type')->default(1)->after('guest_id');
            $table->integer('payment_method')->default(4)->after('amounts_due');
            $table->tinyInteger('rating')->nullable()->after('note');
            $table->boolean('auto_renew')->default(false)->after('rating');
            $table->datetime('auto_renewed_at')->nullable()->after('auto_renew');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('id_type');
            $table->dropColumn('payment_method');
            $table->dropColumn('rating');
            $table->dropColumn('auto_renew');
            $table->dropColumn('auto_renewed_at');
        });
    }
};