<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Apartment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ApartmentResource;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->user()->can('show apartments')) {
            if ($request->user()->can('show reservations'))
                return redirect()->route('reservations.index');

            if ($request->user()->can('show reservations'))
                return redirect()->route('reservations.index');

            if ($request->user()->can('show apartments'))
                return redirect()->route('apartments.index');

            if ($request->user()->can('show finance'))
                return redirect()->route('finance.index');

            if ($request->user()->can('show statistics'))
                return redirect()->route('statistics.index');

            if ($request->user()->can('show users'))
                return redirect()->route('users.index');

            return redirect()->route('profile.edit');
        }

        $apartments = Apartment::query()
            ->with([
                'currentReservation',
                'reservations' => fn ($q) => $q->with(['admin' => fn ($q) => $q->select(['id', 'name',])])->pending()->orderBy('checkin'),
                'records' => fn ($q) => $q->with(['user' => fn ($q) => $q->select(['id', 'name',]), 'reservation'])->latest(),
            ])
            ->orderBy('name')
            ->get();

            $apartmentTypes = collect(config('custom.apartments.types'))->map(fn ($key, $value) => [
                'id' => $value,
                'value' => $key,
            ]);

            $apartmentDescriptions = collect(config('custom.apartments.descriptions'))->map(fn ($key, $value) => [
                'id' => $value,
                'value' => $key,
            ]);

            $idTypes = collect(config('custom.reservations.id_types'))->map(fn ($key, $value) => [
                'id' => $value,
                'value' => $key,
            ]);

            $paymentMethods = collect(config('custom.reservations.payment_methods'))->map(fn ($key, $value) => [
                'id' => $value,
                'value' => $key,
                'image_url' => asset('imgs/' . Str::slug($key) . '.png'),
            ]);

        return Inertia::render('Dashboard', [
            'apartments' => ApartmentResource::collection($apartments),
            'apartmentTypes' => $apartmentTypes,
            'apartmentDescriptions' => $apartmentDescriptions,
            'idTypes' => $idTypes,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}