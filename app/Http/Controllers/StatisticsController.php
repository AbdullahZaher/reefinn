<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Expense;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Enums\ApartmentStateEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\ReservationStateEnum;
use App\Enums\ApartmentStateColorEnum;
use App\Enums\ReservationStateColorEnum;
use Illuminate\Support\Facades\Validator;

class StatisticsController extends Controller
{
    public function index(Request $request) {
        // Filter By Time
        $filters = [
            'time' => ['key' => 'all_time', 'value' => __('All Time')],
        ];

        $validator = Validator::make($request->only(['time']), [
            'time' => ['nullable', 'date_format:M_Y']
        ]);


        if ($request->time && !$validator->fails()) $filters['time'] = ['key' => $request->time, 'value' => __(explode('_', $request->time)[0]) . ' ' . explode('_', $request->time)[1]];

        $apartments = Apartment::query()
            ->with('reservations')
            ->get();

        $expenses = Expense::all();

        if($filters['time']['key'] != 'all_time') {
            $apartments = $apartments->map(function ($apartment) use ($filters) {
                $apartment->reservations = $apartment->reservations->filter(function ($reservation) use ($filters) {
                    return date('M_Y', strtotime($reservation->checkin)) == $filters['time']['key'];
                });

                return $apartment;
            });

            $expenses = $expenses->filter(function ($expense) use ($filters) {
                return date('M_Y', strtotime($expense->date)) == $filters['time']['key'];
            });
        }

        // Data

        $counts = [
            'apartments' => $apartments->count(),
            'reservations' => $apartments->map(fn ($a) => $a->reservations->count())->sum(),
            'users' => \App\Models\User::count(),

            'empty_apartments' => $apartments->where('state', ApartmentStateEnum::Empty->value)->count(),
            'reserved_apartments' => $apartments->where('state', ApartmentStateEnum::Reserved->value)->count(),
            'inhabited_apartments' => $apartments->where('state', ApartmentStateEnum::Inhabited->value)->count(),
            'cleaning_apartments' => $apartments->where('state', ApartmentStateEnum::Cleaning->value)->count(),

            'pending_reservations' => $apartments->map(fn ($a) => $a->reservations->where('state', ReservationStateEnum::Pending->value)->count())->sum(),
            'checkin_reservations' => $apartments->map(fn ($a) => $a->reservations->where('state', ReservationStateEnum::Checkin->value)->count())->sum(),
            'checkout_reservations' => $apartments->map(fn ($a) => $a->reservations->where('state', ReservationStateEnum::Checkout->value)->count())->sum(),
            'canceled_reservations' => $apartments->map(fn ($a) => $a->reservations->where('state', ReservationStateEnum::Canceled->value)->count())->sum(),

            'income' => number_format($apartments->map(fn ($a) => $a->reservations->whereIn('state', [ReservationStateEnum::Checkin->value, ReservationStateEnum::Checkout->value])->map(fn ($r) => $r->total_price)->sum())->sum(), 2),
            'expenses' => number_format($expenses->map(fn ($e) => $e->price)->sum(), 2),
        ];

        $apartments = $apartments->sortByDesc(fn ($a) => $a->reservations->count())
                ->values()
                ->map(function ($apartment) {
                    return [
                        'id' => $apartment->id,
                        'name' => $apartment->name,
                        'reservations_count' => $apartment->reservations->count(),
                        'pending_reservations_count' => $apartment->reservations->where('state', ReservationStateEnum::Pending->value)->count(),
                        'checkin_reservations_count' => $apartment->reservations->where('state', ReservationStateEnum::Checkin->value)->count(),
                        'checkout_reservations_count' => $apartment->reservations->where('state', ReservationStateEnum::Checkout->value)->count(),
                        'canceled_reservations_count' => $apartment->reservations->where('state', ReservationStateEnum::Canceled->value)->count(),
                        'total_profit' => number_format($apartment->reservations->whereIn('state', [ReservationStateEnum::Checkin->value, ReservationStateEnum::Checkout->value])->map(fn ($r) => $r->total_price)->sum(), 2)
                    ];
                })
                ->filter(fn ($a) => $a['reservations_count'] > 0);

        $months_1 = DB::table('reservations')
            ->selectRaw("DISTINCT date_format(JSON_UNQUOTE(JSON_EXTRACT(checkin, '$.gregorian')), '%M %Y') AS period")
            ->get();

        $months_2 = DB::table('expenses')
            ->selectRaw("DISTINCT date_format(JSON_UNQUOTE(JSON_EXTRACT(date, '$.gregorian')), '%M %Y') AS period")
            ->get();

        $months = $months_1->merge($months_2)->unique('period')->pluck('period')->map(fn ($p) => ['key' => date('M_Y', strtotime($p)), 'value' => __(explode(' ', $p)[0]) . ' ' . explode(' ', $p)[1]]);

        return Inertia::render('Statistics', [
            'filters' => $filters,
            'months' => $months,
            'counts' => $counts,
            'apartments' => $apartments,
            'apartmentStateColors' => ApartmentStateColorEnum::toArrayAsc(),
            'reservationStateColors' => ReservationStateColorEnum::toArrayAsc(),
        ]);
    }
}