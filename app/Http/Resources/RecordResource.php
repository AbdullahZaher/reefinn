<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Enums\ApartmentStateEnum;
use Alkoumi\LaravelHijriDate\Hijri;
use App\Http\Resources\ReservationResource;
use Illuminate\Http\Resources\Json\JsonResource;
use JamesMills\LaravelTimezone\Facades\Timezone;

class RecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'state_from' => __(ApartmentStateEnum::tryFrom($this->state_from)->name),
            'state_to' => __(ApartmentStateEnum::tryFrom($this->state_to)->name),
            'reservation' => $this->whenLoaded('reservation', new ReservationResource($this->reservation)),
            'note' => $this->note ?? '-',
            'created_at' => $this->created_at,
        ];
    }
}
