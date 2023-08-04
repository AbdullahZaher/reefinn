<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Enums\ReservationStateEnum;
use App\Enums\ReservationStateColorEnum;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $state_name = ReservationStateEnum::tryFrom($this->state)->name;

        return [
            'id' => $this->id,
            'reservation_number' => $this->reservationNumber,
            'checkin' => $this->checkin,
            'checkout' => $this->checkout,
            'real_checkin' => $this->real_checkin ? $this->real_checkin->format('Y-m-d') : '-',
            'real_checkout' => $this->real_checkout ? $this->real_checkout->format('Y-m-d') : '-',
            'state' => $state_name,
            'state_color' => ReservationStateColorEnum::fromName($state_name),
            'apartment' => ApartmentResource::make($this->whenLoaded('apartment')),
            'guest_id' => $this->guest_id,
            'guest_name' => $this->guest_name,
            'guest_phone' => $this->guest_phone,
            'guest_birthday' => $this->guest_birthday ? $this->guest_birthday : '-',
            'number_of_companions' => $this->number_of_companions,
            'copy' => $this->copy,
            'price_for_night' => number_format($this->price_for_night, 2),
            'total_price' => number_format($this->total_price, 2),
            'discount' => number_format($this->discount, 2),
            'discount_amount' => number_format($this->discount_amount, 2),
            'note' => $this->note,
            'amounts_due' => number_format($this->amounts_due, 2),
            'admin_name' => $this->whenLoaded('admin', fn () => $this->admin->name),
            'created_at' => $this->created_at,
        ];
    }
}