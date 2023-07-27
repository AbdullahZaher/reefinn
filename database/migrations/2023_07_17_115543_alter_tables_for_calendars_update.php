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
        // Missing ordering in previous migrations
        \App\Models\Reservation::whereNull('number_of_companions')->get()->each(fn ($r) => $r->update([
            'number_of_companions' => 0,
        ]));

        DB::statement('ALTER TABLE reservations MODIFY COLUMN guest_birthday DATE AFTER guest_phone');
        DB::statement('ALTER TABLE reservations MODIFY COLUMN number_of_companions TINYINT NOT NULL AFTER guest_birthday');

        // Calendar
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('checkin', 'old_checkin');
            $table->renameColumn('checkout', 'old_checkout');
            $table->renameColumn('guest_birthday', 'old_guest_birthday');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->json('checkin')->nullable()->after('old_checkin');
            $table->json('checkout')->nullable()->after('old_checkout');
            $table->json('guest_birthday')->nullable()->after('old_guest_birthday');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('calendar')->default('gregorian')->after('timezone');
        });

        \App\Models\Reservation::all()->each(function ($r) {
            if ($r->old_guest_birthday) {
                $r->update([
                    'checkin' => $r->old_checkin,
                    'checkout' => $r->old_checkout,
                    'guest_birthday' => $r->old_guest_birthday,
                ]);
            }
            else {
                $r->update([
                    'checkin' => $r->old_checkin,
                    'checkout' => $r->old_checkout,
                ]);
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('old_checkin');
            $table->dropColumn('old_checkout');
            $table->dropColumn('old_guest_birthday');

            $table->json('checkin')->nullable(false)->change();
            $table->json('checkout')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->json('checkin')->nullable()->after('old_checkin');
            $table->json('checkout')->nullable()->after('old_checkout');
            $table->json('guest_birthday')->nullable()->after('old_guest_birthday');

            $table->renameColumn('old_checkin', 'checkin');
            $table->renameColumn('old_checkout', 'checkout');
            $table->renameColumn('old_guest_birthday', 'guest_birthday');
        });

        \App\Models\Reservation::all()->each(fn ($r) => $r->update([
            'old_checkin' => json_decode($r->getRawAttribute('checkin'))->gregorian,
            'old_checkout' => json_decode($r->getRawAttribute('checkout'))->gregorian,
            'old_guest_birthday' => json_decode($r->getRawAttribute('guest_birthday'))->gregorian,
        ]));

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('checkin');
            $table->dropColumn('checkout');
            $table->dropColumn('guest_birthday');

            $table->renameColumn('old_checkin', 'checkin');
            $table->renameColumn('old_checkout', 'checkout');
            $table->renameColumn('old_guest_birthday', 'guest_birthday');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('calendar');
        });
    }
};
