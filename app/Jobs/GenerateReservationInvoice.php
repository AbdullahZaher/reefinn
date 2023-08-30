<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Browsershot\Browsershot;

class GenerateReservationInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Reservation $reservation;
    protected bool $firstTimeGenerating;

    /**
     * Create a new job instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
        $this->firstTimeGenerating = $reservation->invoice_file_name === null;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filename = $this->generateFileName();

        Browsershot::html((new \App\Services\InvoiceService())->generateReservationInvoiceHtml($this->reservation))
            ->showBackground()
            ->margins(0, 0, 0, 0)
            ->format('A4')
            ->newHeadless()
            ->savePdf('storage/reservations/invoices/' . $filename);

        $this->reservation->update([
            'invoice_file_name' => $filename,
        ]);
    }

    protected function generateFileName(): string
    {
        return $this->reservation->uuid . '.pdf';
    }
}