<?php

namespace App\Http\Controllers;

use DateTime;
use Inertia\Inertia;
use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\ReservationStateEnum;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\ReservationResource;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Components\Calendar;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $apartments = collect(Apartment::all());

        $filters = [
            'apartments' => !!strlen((string) $request->get('apartments')) ? explode(",", (string) $request->get('apartments')) : $apartments->pluck('id'),
        ];

        auth()->user()->calendar = 'gregorian';

        $reservations = Reservation::query()
                            ->with(['apartment',])
                            ->whereIn('state', [ReservationStateEnum::Pending, ReservationStateEnum::Checkin,])
                            ->whereHas('apartment', fn ($query) => $query->whereIn('id', $filters['apartments']))
                            ->get();

        $idTypes = collect(config('custom.reservations.id_types'))->map(fn ($key, $value) => [
            'id' => $value,
            'value' => $key,
        ]);

        $paymentMethods = collect(config('custom.reservations.payment_methods'))->map(fn ($key, $value) => [
            'id' => $value,
            'value' => $key,
            'image_url' => asset('imgs/' . Str::slug($key) . '.png'),
        ]);

        return Inertia::render('Calendar', [
            'filters' => $filters,
            'apartments' => ApartmentResource::collection($apartments),
            'reservations' => ReservationResource::collection($reservations),
            'reservationTypes' => config('custom.reservations.types'),
            'idTypes' => $idTypes,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function icalendar($apartment) {
        $apartment = Apartment::query()
                            ->with([
                                'reservations' => fn ($query) => $query->whereIn('state', [ReservationStateEnum::Pending, ReservationStateEnum::Checkin,]),
                            ])
                            ->findOrFail($apartment);

        if (auth()->check()) auth()->user()->calendar = 'gregorian';
        app()->setLocale('en');

        $id_types = collect(config('custom.reservations.id_types'));

        $events = $apartment->reservations->map(function ($reservation) use ($id_types) {
            return Event::create()
                ->name(ReservationStateEnum::tryFrom($reservation->state)->name)
                ->description(__('Guest Name') . ': ' . $reservation->guest_name . ', ' . __('Guest ID Type') . ': ' . __($id_types[$reservation->id_type]) . ', ' . __('Guest ID') . ': ' . $reservation->guest_id, ', ' . __('Guest Phone') . ': ' . $reservation->guest_phone)
                ->status($reservation->state == ReservationStateEnum::Checkin->value ? EventStatus::confirmed() : EventStatus::tentative())
                ->period(new DateTime($reservation->checkin), new DateTime($reservation->checkout))
                ->fullDay()
                ->uniqueIdentifier($reservation->id)
                ->createdAt(new DateTime($reservation->getRawOriginal('created_at')));
        })->toArray();

        return Calendar::create($apartment->name)
            ->event($events)
            ->get();
    }
}