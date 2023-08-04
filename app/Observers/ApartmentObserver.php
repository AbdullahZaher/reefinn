<?php

namespace App\Observers;

use App\Models\Apartment;
use App\Models\ApartmentRecord;

class ApartmentObserver
{
    public function updating(Apartment $apartment): void
    {
        $state_from = $apartment->getOriginal('state');
        $state_to = $apartment->state;

        if ($state_from != $state_to) {
            ApartmentRecord::create([
                'state_from' => $state_from,
                'state_to' => $state_to,
                'reservation_id' => $apartment->current_reservation_id ?? $apartment->getOriginal('current_reservation_id'),
                'apartment_id' => $apartment->id,
                'user_id' => auth()->id(),
            ]);
        }
    }
}