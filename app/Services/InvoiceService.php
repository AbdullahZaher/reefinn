<?php

namespace App\Services;

use App\Models\Reservation;
use Salla\ZATCA\Tags\Seller;
use Illuminate\Support\Carbon;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\TaxNumber;
use App\Settings\GeneralSettings;
use Salla\ZATCA\Tags\InvoiceDate;
use Alkoumi\LaravelHijriDate\Hijri;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;

class InvoiceService {
    protected GeneralSettings $settings;

    public function __construct()
    {
        $this->settings = app(GeneralSettings::class);
    }

    public function generateReservationInvoiceHtml(Reservation $reservation): string
    {
        return $this->generateReservationInvoiceView($reservation)->render();
    }

    public function generateReservationInvoiceView(Reservation $reservation): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $hotel = [
            'logo' => $this->settings->logo ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($this->settings->logo))) : null,
        ];

        $calendar =  auth()->user()?->calendar ?? 'gregorian';

        $checkin = json_decode($reservation->getRawOriginal('checkin'))->gregorian;
        $checkout = json_decode($reservation->getRawOriginal('checkout'))->gregorian;
        $guest_birthday = json_decode($reservation->getRawOriginal('guest_birthday'))->gregorian;

        $attrs = [
            'checkin' => $calendar == 'hijri' ? Hijri::Date('j F Y', $checkin) : Carbon::parse($checkin)->translatedFormat('d F Y'),
            'checkout' => $calendar == 'hijri' ? Hijri::Date('j F Y', $checkout) : Carbon::parse($checkout)->translatedFormat('d F Y'),
            'guest_birthday' => $calendar == 'hijri' ? Hijri::Date('j F Y', $guest_birthday) : Carbon::parse($guest_birthday)->translatedFormat('d F Y'),
            'reservation_lease_terms' => $this->settings->reservation_lease_terms[$reservation->type][app()->getLocale() ?? 'en'],
        ];

        return view('pdfs.reservation_invoice', [
            'hotel' => $hotel,
            'reservation' => $reservation,
            'attrs' => $attrs,
        ]);
    }

    public function generateReservationReceiptVoucherHtml(Reservation $reservation): string
    {
        return $this->generateReservationReceiptVoucherView($reservation)->render();
    }

    public function generateReservationReceiptVoucherView(Reservation $reservation): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $hotel = [
            'name' => $this->settings->hotel_name,
            'logo' => $this->settings->logo ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($this->settings->logo))) : null,
        ];

        $checkin = json_decode($reservation->getRawOriginal('checkin'))->gregorian;
        $checkout = json_decode($reservation->getRawOriginal('checkout'))->gregorian;

        return view('pdfs.reservation_receipt_voucher', [
            'hotel' => $hotel,
            'reservation' => $reservation,
            'attrs' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
            ],
        ]);
    }


    public function generateReservationTaxInvoiceHtml(Reservation $reservation): string
    {
        return $this->generateReservationTaxInvoiceView($reservation)->render();
    }

    public function generateReservationTaxInvoiceView(Reservation $reservation): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $reservation->load('taxes');

        $hotel = [
            'name' => $this->settings->hotel_name,
            'logo' => $this->settings->logo ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($this->settings->logo))) : null,
            'commercial_register' => $this->settings->commercial_register,
            'tax_number' => $this->settings->tax_number,
            'phone' => $this->settings->phone,
        ];

        $calendar =  auth()->user()?->calendar ?? 'gregorian';

        $checkin = json_decode($reservation->getRawOriginal('checkin'))->gregorian;
        $checkout = json_decode($reservation->getRawOriginal('checkout'))->gregorian;

        $displayQRCodeAsBase64 = GenerateQrCode::fromArray([
            new Seller($hotel['name']),
            new TaxNumber($hotel['tax_number']),
            new InvoiceDate($reservation->getRawOriginal('created_at')),
            new InvoiceTotalAmount(number_format($reservation->total_price, 2, '.', '')),
            new InvoiceTaxAmount(number_format($reservation->taxes_amount, 2, '.', '')),
        ])->render();

        $attrs = [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'displayQRCodeAsBase64' => $displayQRCodeAsBase64,
        ];

        return view('pdfs.reservation_tax_invoice', [
            'hotel' => $hotel,
            'reservation' => $reservation,
            'attrs' => $attrs,
        ]);
    }
}