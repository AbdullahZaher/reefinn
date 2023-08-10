<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\ApartmentStateEnum;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Alkoumi\LaravelHijriDate\Hijri;
use App\Enums\ReservationStateEnum;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $states = collect(ReservationStateEnum::toArray());

        $filters = [
            'states' => !!strlen((string) $request->get('states')) ? explode(",", (string) $request->get('states')) : $states->pluck('key'),
        ];

        $reservations = Reservation::query()
            ->with([
                'apartment',
                'admin' => fn ($q) => $q->select(['id', 'name']),
            ])
            ->whereIn('state', $filters['states'])
            ->latest()
            ->paginate(50);

        if ($request->wantsJson())
            return ReservationResource::collection($reservations);

        $idTypes = collect(config('custom.reservations.id_types'))->map(fn ($key, $value) => [
            'id' => $value,
            'value' => $key,
        ]);

        $paymentMethods = collect(config('custom.reservations.payment_methods'))->map(fn ($key, $value) => [
            'id' => $value,
            'value' => $key,
            'image_url' => asset('imgs/' . Str::slug($key) . '.png'),
        ]);

        return Inertia::render('Reservations', [
            'filters' => $filters,
            'reservations' => ReservationResource::collection($reservations),
            'states' => $states,
            'idTypes' => $idTypes,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function invoice(GeneralSettings $settings, Request $request, Reservation $reservation)
    {
        abort_if(!$request->wantsJson(), 404);

        $hotel = [
            'logo' => $settings->logo ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path(app(\App\Settings\GeneralSettings::class)->logo))) : null,
        ];

        $calendar = auth()->user()->calendar;

        $checkin = json_decode($reservation->getRawOriginal('checkin'))->gregorian;
        $checkout = json_decode($reservation->getRawOriginal('checkout'))->gregorian;
        $guest_birthday = json_decode($reservation->getRawOriginal('guest_birthday'))->gregorian;

        $attrs = [
            'checkin' => $calendar == 'hijri' ? Hijri::Date('j F Y', $checkin) : Carbon::parse($checkin)->translatedFormat('d F Y'),
            'checkout' => $calendar == 'hijri' ? Hijri::Date('j F Y', $checkout) : Carbon::parse($checkout)->translatedFormat('d F Y'),
            'guest_birthday' => $calendar == 'hijri' ? Hijri::Date('j F Y', $guest_birthday) : Carbon::parse($guest_birthday)->translatedFormat('d F Y'),
            'reservation_lease_terms' => $settings->reservation_lease_terms[app()->getLocale()],
        ];

        return view('pdfs.reservation_invoice', [
            'hotel' => $hotel,
            'reservation' => $reservation,
            'attrs' => $attrs,
        ]);
    }

    public function store(ReservationRequest $request, Apartment $apartment)
    {
        if ($apartment->state == ApartmentStateEnum::Maintenance->value) return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Can\'t add reservation for maintenance apartment.')]);

        if (!self::check_reservation_date($request->validated()['checkin'], $request->validated()['checkout'], $apartment))
            throw ValidationException::withMessages(['checkin' => __('There is a reservation in this date.')]);

        DB::transaction(function () use ($request, $apartment) {
            $data = $request->validated() + ['admin_id' => auth()->id()];

            if (!$request->user()->can('change reservation price for night')) $data['price_for_night'] = $apartment->price_for_night;

            $data['number_of_nights'] = Carbon::parse($data['checkout'])->diffInDays(Carbon::parse($data['checkin']));
            if ($request->validated()['checkin'] == $request->validated()['checkout']) $data['number_of_nights'] = 1;

            $data['total_price'] = $data['price_for_night'] * $data['number_of_nights'];

            $data['discount'] = floor($data['discount'] * 100) / 100;
            $data['discount_amount'] =  $data['discount'] / 100 * $data['total_price'];
            $data['discount_amount'] = floor($data['discount_amount'] * 100) / 100;
            $data['total_price'] -= $data['discount_amount'];

            if ($data['checkin_now'] && ($apartment->state == ApartmentStateEnum::Empty->value || $apartment->state == ApartmentStateEnum::Reserved->value)) {
                unset($data['checkin_now']);

                $reservation = Reservation::create($data + [
                    'apartment_id' => $apartment->id,
                    'state' => ReservationStateEnum::Checkin->value,
                ]);

                $apartment->update([
                    'state' => ApartmentStateEnum::Inhabited->value,
                    'current_reservation_id' => $reservation->id,
                ]);
            } else {
                unset($data['checkin_now']);

                $reservation = Reservation::create($data + ['apartment_id' => $apartment->id, 'state' => ReservationStateEnum::Pending->value]);

                if ($apartment->state == ApartmentStateEnum::Empty->value) {
                    $apartment->update([
                        'state' => ApartmentStateEnum::Reserved->value,
                    ]);
                }
            }
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation has been added.')]);
    }

    public function transfer(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'apartment_id' => ['required', 'integer', 'exists:apartments,id'],
        ]);

        if ($reservation->state != ReservationStateEnum::Pending->value && $reservation->state != ReservationStateEnum::Checkin->value)
            throw ValidationException::withMessages(['apartment_id' => __('Can\'t transfer this reservation.')]);

        $apartment = Apartment::find($validated['apartment_id']);
        if (!self::check_reservation_date($reservation->checkin, $reservation->checkout, $apartment))
            throw ValidationException::withMessages(['apartment_id' => __('There is a reservation in this date.')]);

        if ($reservation->apartment->id != $apartment->id) {
            $reservation->load('apartment');

            DB::transaction(function () use ($reservation, $apartment) {
                if ($reservation->state == ReservationStateEnum::Checkin->value) {
                    $reservation->apartment->update([
                        'state' => ApartmentStateEnum::Cleaning->value,
                        'current_reservation_id' => null,
                    ]);

                    if ($apartment->state == ApartmentStateEnum::Empty->value || $apartment->state == ApartmentStateEnum::Reserved->value) {
                        $apartment->update([
                            'state' => ApartmentStateEnum::Inhabited->value,
                            'current_reservation_id' => $reservation->id,
                        ]);

                        $reservation->update([
                            'apartment_id' => $apartment->id,
                        ]);
                    } else {
                        $reservation->update([
                            'state' => ReservationStateEnum::Pending->value,
                            'apartment_id' => $apartment->id,
                        ]);
                    }
                } else {
                    if ($reservation->apartment->state == ApartmentStateEnum::Reserved->value && $reservation->apartment->reservations()->pending()->count() == 1) {
                        $reservation->apartment->update([
                            'state' => ApartmentStateEnum::Empty->value,
                            'current_reservation_id' => null,
                        ]);
                    }

                    if ($apartment->state == ApartmentStateEnum::Empty->value) {
                        $apartment->update([
                            'state' => ApartmentStateEnum::Reserved->value,
                        ]);
                    }

                    $reservation->update([
                        'apartment_id' => $apartment->id,
                    ]);
                }
            });
        }

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation has been transferred.')]);
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $reservation->load('apartment');

        if ($reservation->apartment->state == ApartmentStateEnum::Maintenance->value) return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Can\'t edit reservation for maintenance apartment.')]);
        if ($reservation->state != ReservationStateEnum::Pending->value && $reservation->state != ReservationStateEnum::Checkin->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('There has been an error')]);

        if (!self::check_reservation_date($request->validated()['checkin'], $request->validated()['checkout'], $reservation->apartment, $reservation->id))
            throw ValidationException::withMessages(['checkin' => __('There is a reservation in this date.')]);

        $data = $request->validated();

        if (!$request->user()->can('change reservation price for night')) $data['price_for_night'] = $reservation->price_for_night;

        $data['number_of_nights'] = Carbon::parse($data['checkout'])->diffInDays(Carbon::parse($data['checkin']));
        if ($request->validated()['checkin'] == $request->validated()['checkout']) $data['number_of_nights'] = 1;

        $data['total_price'] = $data['price_for_night'] * $data['number_of_nights'];

        $data['discount'] = floor($data['discount'] * 100) / 100;
        $data['discount_amount'] =  $data['discount'] / 100 * $data['total_price'];
        $data['discount_amount'] = floor($data['discount_amount'] * 100) / 100;
        $data['total_price'] -= $data['discount_amount'];

        $reservation->update($data);

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation has been updated.')]);
    }

    public function checkin(Reservation $reservation)
    {
        $reservation->load('apartment');

        if ($reservation->apartment->state == ApartmentStateEnum::Inhabited->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('There is a guest already in this apartment')]);

        if ($reservation->apartment->state != ApartmentStateEnum::Reserved->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('There has been an error')]);

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'state' => ReservationStateEnum::Checkin->value,
                'real_checkin' => now(),
            ]);

            $reservation->apartment->update([
                'state' => ApartmentStateEnum::Inhabited->value,
                'current_reservation_id' => $reservation->id,
            ]);
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation has been updated.')]);
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->load('apartment');

        DB::transaction(function () use ($reservation) {
            if ($reservation->apartment->state == ApartmentStateEnum::Reserved->value && $reservation->apartment->reservations()->pending()->count() == 1) {
                $reservation->apartment->update([
                    'state' => ApartmentStateEnum::Empty->value,
                    'current_reservation_id' => null,
                ]);
            }

            $reservation->update([
                'state' => ReservationStateEnum::Canceled->value,
            ]);
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation has been canceled.')]);
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->state == ReservationStateEnum::Checkin->value) return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('Cannot delete a checkin reservation.')]);


        DB::transaction(function () use ($reservation) {
            if ($reservation->apartment->state == ApartmentStateEnum::Reserved->value && $reservation->apartment->reservations()->pending()->count() == 1) {
                $reservation->apartment->update([
                    'state' => ApartmentStateEnum::Empty->value,
                    'current_reservation_id' => null,
                ]);
            }

            $reservation->delete();
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation has been deleted.')]);
    }

    static function check_reservation_date($checkin, $checkout, $apartment, $reservation_id = null): bool
    {
        return !Reservation::query()
            ->when($reservation_id, fn ($query) => $query->where('id', '!=', $reservation_id))
            ->where('apartment_id', $apartment->id)
            ->where('checkin->gregorian', '<=', $checkin)
            ->where('checkout->gregorian', '>', $checkin)
            ->whereColumn('checkin->gregorian', '!=', 'checkout->gregorian')
            ->whereIn('state', [ReservationStateEnum::Pending->value, ReservationStateEnum::Checkin->value])
            ->exists()
            &&
            !Reservation::query()
                ->when($reservation_id, fn ($query) => $query->where('id', '!=', $reservation_id))
                ->where('apartment_id', $apartment->id)
                ->where('checkin->gregorian', '<', $checkout)
                ->where('checkout->gregorian', '>=', $checkout)
                ->whereColumn('checkin->gregorian', '!=', 'checkout->gregorian')
                ->whereIn('state', [ReservationStateEnum::Pending->value, ReservationStateEnum::Checkin->value])
                ->exists();
    }

    public function update_terms(GeneralSettings $settings, Request $request) {
        $validated = $request->validate([
            'terms' => ['required', 'array',],
            'terms.en' => ['required', 'string', 'max:3500',],
            'terms.ar' => ['required', 'string', 'max:3500',],
        ]);

        $settings->reservation_lease_terms = [
            'en' => $validated['terms']['en'],
            'ar' => $validated['terms']['ar'],
        ];
        $settings->save();

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Reservation terms has been updated.')]);
    }
}