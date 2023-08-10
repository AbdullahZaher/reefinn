<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Enums\ApartmentStateEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\ReservationStateEnum;
use App\Http\Requests\ApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Http\Controllers\ReservationController;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $states = collect(ApartmentStateEnum::toArray());

        $filters = [
            'states' => !!strlen((string) $request->get('states')) ? explode(",", (string) $request->get('states')) : $states->pluck('key'),
        ];

        $apartments = Apartment::query()
            ->whereIn('state', $filters['states'])
            ->orderBy('name')
            ->paginate(50);

        if ($request->wantsJson())
            return ApartmentResource::collection($apartments);

        $apartmentTypes = collect(config('custom.apartments.types'))->map(fn ($key, $value) => [
            'id' => $value,
            'value' => $key,
        ]);

        $apartmentDescriptions = collect(config('custom.apartments.descriptions'))->map(fn ($key, $value) => [
            'id' => $value,
            'value' => $key,
        ]);

        return Inertia::render('Apartments', [
            'filters' => $filters,
            'apartments' => ApartmentResource::collection($apartments),
            'states' => $states,
            'apartmentTypes' => $apartmentTypes,
            'apartmentDescriptions' => $apartmentDescriptions,
        ]);
    }

    public function store(ApartmentRequest $request)
    {
        Apartment::create($request->validated());

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been added.')]);
    }

    public function available(Request $request)
    {
        $validated = $request->validate([
            'checkin' => ['required', 'date'],
            'checkout' => ['required', 'date'],
        ]);

        $apartments = Apartment::query()
            ->with(['reservations'])
            ->orderBy('name')
            ->get()
            ->filter(fn ($apartment) => ReservationController::check_reservation_date($validated['checkin'], $validated['checkout'], $apartment));

        return ApartmentResource::collection($apartments);
    }

    public function update(ApartmentRequest $request, Apartment $apartment)
    {
        $apartment->update($request->validated());

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been updated.')]);
    }

    public function update_state(Request $request, Apartment $apartment)
    {
        if ($apartment->state == ApartmentStateEnum::Empty->value || $apartment->state == ApartmentStateEnum::Reserved->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('There has been an error')]);

        if ($apartment->state === ApartmentStateEnum::Cleaning->value) {
            abort_if(!$request->user()->can('empty apartments'), 403);

            $validated = $request->validate([
                'note' => ['nullable', 'string', 'max:500'],
            ]);

            $state = $apartment->reservations()->pending()->count() > 0 ? ApartmentStateEnum::Reserved->value : ApartmentStateEnum::Empty->value;

            $apartment->update([
                'state' => $state,
                'current_reservation_id' => null,
            ]);

            $apartment->records()->where('state_from', ApartmentStateEnum::Cleaning->value)->latest()->first()->update([
                'note' => $validated['note'],
            ]);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been updated to') . ' ' . __(ApartmentStateEnum::tryFrom($state)->name)]);
        } else if ($apartment->state === ApartmentStateEnum::Inhabited->value) {
            abort_if(!$request->user()->can('checkout apartments'), 403);

            $validated = $request->validate([
                'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            ]);

            DB::transaction(function () use ($apartment, $validated) {
                $apartment->currentReservation->update([
                    'state' => ReservationStateEnum::Checkout->value,
                    'real_checkout' => now(),
                    'rating' => $validated['rating'],
                ]);

                $apartment->update([
                    'state' => ApartmentStateEnum::Cleaning->value,
                    'current_reservation_id' => null,
                ]);
            });

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been updated to') . ' ' . __(ApartmentStateEnum::tryFrom($apartment->state)->name)]);
        }
    }

    public function update_maintenance(Apartment $apartment)
    {
        if ($apartment->state == ApartmentStateEnum::Inhabited->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('Cannot update apartment to maintenance while it is inhabited.')]);
        if ($apartment->state == ApartmentStateEnum::Cleaning->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('Cannot update apartment to maintenance while it is being cleaned.')]);

        if ($apartment->state == ApartmentStateEnum::Maintenance->value) {
            $apartment->update([
                'state' => $apartment->reservations()->pending()->count() > 0 ? ApartmentStateEnum::Reserved->value : ApartmentStateEnum::Empty->value,
            ]);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been updated to') . ' ' . __(ApartmentStateEnum::tryFrom($apartment->state)->name)]);
        } else {
            $apartment->update([
                'state' => ApartmentStateEnum::Maintenance->value,
                'current_reservation_id' => null,
            ]);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been updated to') . ' ' . __(ApartmentStateEnum::tryFrom($apartment->state)->name)]);
        }
    }

    public function destroy(Apartment $apartment)
    {
        DB::transaction(function () use ($apartment) {
            $apartment->records()->delete();
            $apartment->reservations()->delete();
            $apartment->delete();
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Apartment has been deleted.')]);
    }
}