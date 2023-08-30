<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Services\WhatsappService;
use App\Settings\GeneralSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendWelcomeMessageToGuest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff= 30;

    private Reservation $reservation;
    private string $hotel_name;
    private string $hotel_phone;

    /**
     * Create a new job instance.
     */
    public function __construct(int $reservation_id)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        $settings = app(GeneralSettings::class);
        $this->hotel_name = $settings->hotel_name;
        $this->hotel_phone = $settings->phone;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hotel_name = $this->hotel_name;
        $hotel_phone = $this->hotel_phone;
        $checkin_date = json_decode($this->reservation->getRawOriginal('checkin'))->gregorian;
        $apartment_name = $this->reservation->apartment->name;
        $total_price = $this->reservation->total_price;
        $invoice_url = route('reservations.invoice.print', $this->reservation->uuid);

        $result = (new WhatsappService())->sendWelcomeMessage(Str::replace('+', '', $this->reservation->guest_phone), [
            "hotel_name" => $hotel_name,
            "hotel_phone" => $hotel_phone,
            "checkin_date" => $checkin_date,
            "apartment_name" => $apartment_name,
            "total_price" => $total_price,
            "invoice_url" => $invoice_url,
        ]);

        if (!$result) throw new \Exception('Failed to send message');
    }
}