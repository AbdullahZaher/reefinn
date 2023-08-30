<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function reservation_invoice($reservation)
    {
        $reservation = Reservation::where('uuid', $reservation)->firstOrFail();

        return view('invoice', [
            'html' => (new InvoiceService())->generateReservationInvoiceHtml($reservation),
        ]);
    }

    public function receipt_voucher($reservation)
    {
        $reservation = Reservation::where('uuid', $reservation)->firstOrFail();

        return view('invoice', [
            'html' => (new InvoiceService())->generateReservationReceiptVoucherHtml($reservation),
        ]);
    }

    public function tax_invoice($reservation)
    {
        $reservation = Reservation::where('uuid', $reservation)->firstOrFail();

        return view('invoice', [
            'html' => (new InvoiceService())->generateReservationTaxInvoiceHtml($reservation),
        ]);
    }
}