<?php

use Illuminate\Support\Carbon;
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
            $table->integer('r_id')->after('id')->nullable();
            $table->foreignId('admin_id')->after('apartment_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('number_of_nights')->after('price_for_night')->default(0);
            $table->decimal('discount', 16, 2)->after('total_price')->default(0.00);
            $table->decimal('discount_amount', 16, 2)->after('discount')->default(0.00);
            $table->decimal('amounts_due', 16, 2)->after('discount_amount')->default(0.00);
            $table->softDeletes();
        });

        DB::table('reservations')->get()->each(function ($reservation) {
            $checkin = json_decode($reservation->checkin)->gregorian;
            $checkout = json_decode($reservation->checkout)->gregorian;
            $number_of_nights = Carbon::parse($checkout)->diffInDays(Carbon::parse($checkin));

            DB::table('reservations')->where('id', $reservation->id)->update([
                'r_id' => rand(100, 999),
                'admin_id' => 1,
                'number_of_nights' => $number_of_nights > 0 ? $number_of_nights : 1,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign('reservations_admin_id_foreign');
            $table->dropColumn('r_id');
            $table->dropColumn('admin_id');
            $table->dropColumn('discount');
            $table->dropColumn('amounts_due');
            $table->dropSoftDeletes();
        });
    }
};
