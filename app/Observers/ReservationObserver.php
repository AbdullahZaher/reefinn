<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Models\ReservationRecord;

class ReservationObserver
{
    public function updating(Reservation $reservation): void
    {
        $state_from = $reservation->getOriginal('state');
        $state_to = $reservation->state;

        $old_data = collect($reservation->getRawOriginal())->except(['id', 'state', 'total_price', 'admin_id', 'created_at', 'updated_at']);
        $new_data = collect($reservation->getAttributes())->except(['id', 'state', 'total_price', 'admin_id', 'created_at', 'updated_at']);

        $changes = $new_data->diff($old_data);

        if ($changes->count() || $state_from != $state_to) {
            ReservationRecord::create([
                'state_from' => $state_from,
                'state_to' => $state_to,
                'old_data' => $old_data,
                'new_data' => $changes,
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
            ]);
        }
    }
}