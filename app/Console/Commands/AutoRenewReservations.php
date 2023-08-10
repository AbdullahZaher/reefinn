<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Console\Command;
use App\Settings\GeneralSettings;
use App\Enums\ReservationStateEnum;

class AutoRenewReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a day to all reservations that are checked in and have auto renew enabled.';

    /**
     * Execute the console command.
     */
    public function handle(GeneralSettings $settings)
    {
        $checkout_default_time = $settings->checkout_default_time;

        $running_time = Carbon::parse('today midnight')->addHours(explode(':', $checkout_default_time)[0] + $settings->auto_renew_after)->addMinutes(explode(':', $checkout_default_time)[1]);
        $now = Carbon::now($settings->timezone);

        if (strtotime($now->toDateTimeString()) >= $running_time->timestamp) {
            Reservation::where('state', ReservationStateEnum::Checkin)
            ->where('auto_renew', true)
            ->where('checkout->gregorian', $now->format('Y-m-d'))
            ->chunk(100, function ($reservations) {
                foreach ($reservations as $reservation) {
                    $discount_amount_for_night = $reservation->discount * $reservation->price_for_night / 100;
                    $total_price_for_night = $reservation->price_for_night - $discount_amount_for_night;

                    $total_price = $reservation->total_price + $total_price_for_night;
                    $discount_amount = $reservation->discount_amount + $discount_amount_for_night;

                    $reservation->update([
                        'auto_renewed_at' => now(),
                        'checkout' => Carbon::createFromFormat('Y-m-d', $reservation->checkout)->addDays(1)->format('Y-m-d'),
                        'number_of_nights' => $reservation->number_of_nights + 1,
                        'total_price' => $total_price,
                        'discount_amount' => $discount_amount,
                        'amounts_due' => $total_price_for_night,
                    ]);
                }
            });
        }

        $this->info('Auto renew reservations command ran successfully at: ' . $now->toDateTimeString());
    }
}