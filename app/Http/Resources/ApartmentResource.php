<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\ApartmentStateEnum;
use App\Http\Resources\ReservationResource;
use App\Enums\ApartmentStateColorEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use JamesMills\LaravelTimezone\Facades\Timezone;

class ApartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $state_name = ApartmentStateEnum::tryFrom($this->state)->name;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => config('custom.apartments.types.' . $this->type),
            'description' => config('custom.apartments.descriptions.' . $this->description),
            'type_id' => $this->type,
            'description_id' => $this->description,
            'price_for_night' => number_format($this->price_for_night, 2),
            'note' => $this->note,
            'state' => $state_name,
            'state_color' => ApartmentStateColorEnum::fromName($state_name),
            'days_left' => $this->whenLoaded('currentReservation', function () {
                $checkout = json_decode($this->currentReservation->getRawOriginal('checkout'))->gregorian;

                return Carbon::parse(Timezone::convertToLocal(now(), 'Y-m-d')) < $checkout ? Carbon::parse(Timezone::convertToLocal(now(), 'Y-m-d'))->diffInDays($checkout) : 0;
            }),
            'current_reservation' => ReservationResource::make($this->whenLoaded('currentReservation')),
            'reservations' => $request->user()->can('show reservations') ? ReservationResource::collection($this->whenLoaded('reservations')) : null,
            'records' => $request->user()->can('show records') ? RecordResource::collection($this->whenLoaded('records')) : null,
            'created_at' => $this->created_at,
        ];
    }
}