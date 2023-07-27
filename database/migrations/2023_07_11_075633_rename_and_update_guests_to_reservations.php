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
        Schema::rename('guests', 'reservations');

        Schema::table('reservations', function (Blueprint $table) {
            $table->date('guest_birthday')->nullable()->after('phone');
            $table->integer('number_of_companions')->nullable()->after('guest_birthday');
            $table->datetime('real_checkin')->nullable()->after('checkout');
            $table->datetime('real_checkout')->nullable()->after('real_checkin');
            $table->tinyInteger('state')->default(0)->after('real_checkout');

            $table->renameColumn('name', 'guest_name');
            $table->renameColumn('phone', 'guest_phone');
        });

        DB::statement('ALTER TABLE reservations MODIFY COLUMN guest_id VARCHAR(255) AFTER state');
        DB::statement('ALTER TABLE reservations MODIFY COLUMN guest_name VARCHAR(255) AFTER guest_id');
        DB::statement('ALTER TABLE reservations MODIFY COLUMN guest_phone VARCHAR(255) AFTER guest_name');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('guest_name', 'name');
            $table->renameColumn('guest_phone', 'phone');

            $table->dropColumn('guest_birthday');
            $table->dropColumn('number_of_companions');
            $table->dropColumn('real_checkin');
            $table->dropColumn('real_checkout');
            $table->dropColumn('state');
        });

        Schema::rename('reservations', 'guests');

        Schema::table('apartments', function (Blueprint $table) {
            $table->dropForeign('apartments_current_guest_id_foreign');
            $table->foreign('current_guest_id')->nullable()->references('id')->on('guests');
        });

        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign('records_guest_id_foreign');
            $table->foreign('guest_id')->nullable()->references('id')->on('guests');
        });
    }
};