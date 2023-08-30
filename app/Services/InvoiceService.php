<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Carbon;
use App\Settings\GeneralSettings;
use Alkoumi\LaravelHijriDate\Hijri;

class InvoiceService {
    protected GeneralSettings $settings;
    protected Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->settings = app(GeneralSettings::class);
        $this->reservation = $reservation;
    }

    public function generateReservationView(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $hotel = [
            'logo' => $this->settings->logo ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($this->settings->logo))) : null,
        ];

        $calendar =  auth()->user()?->calendar ?? 'gregorian';

        $checkin = json_decode($this->reservation->getRawOriginal('checkin'))->gregorian;
        $checkout = json_decode($this->reservation->getRawOriginal('checkout'))->gregorian;
        $guest_birthday = json_decode($this->reservation->getRawOriginal('guest_birthday'))->gregorian;

        $attrs = [
            'checkin' => $calendar == 'hijri' ? Hijri::Date('j F Y', $checkin) : Carbon::parse($checkin)->translatedFormat('d F Y'),
            'checkout' => $calendar == 'hijri' ? Hijri::Date('j F Y', $checkout) : Carbon::parse($checkout)->translatedFormat('d F Y'),
            'guest_birthday' => $calendar == 'hijri' ? Hijri::Date('j F Y', $guest_birthday) : Carbon::parse($guest_birthday)->translatedFormat('d F Y'),
            'reservation_lease_terms' => $this->settings->reservation_lease_terms[app()->getLocale() ?? 'en'],
        ];

        return view('pdfs.reservation_invoice', [
            'hotel' => $hotel,
            'reservation' => $this->reservation,
            'attrs' => $attrs,
        ]);
    }

    public function generateReservationHtml(): string
    {
        return $this->generateReservationView()->render();
    }
}