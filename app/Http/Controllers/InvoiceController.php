<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($reservation)
    {
        $reservation = Reservation::where('uuid', $reservation)->firstOrFail();

        return view('invoice', [
            'html' => (new InvoiceService($reservation))->generateReservationHtml(),
        ]);
    }
}