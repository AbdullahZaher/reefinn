<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(GeneralSettings $settings, Request $request): Response
    {
        $hotel = [
            'hotel_name' => $settings->hotel_name,
            'logo' => $settings->logo ? asset($settings->logo) : null,
            'branch_no' => $settings->branch_no,
            'phone' => $settings->phone,
            'commercial_register' => $settings->commercial_register,
            'tax_number' => $settings->tax_number,
            'checkout_default_time' => $settings->checkout_default_time,
            'auto_renew_after' => $settings->auto_renew_after,
            'timezone' => $settings->timezone,
            'location' => $settings->location,
        ];

        $calendars = [
            [
                'value' => 'gregorian',
                'label' => __('Gregorian'),
            ],
            [
                'value' => 'hijri',
                'label' => __('Hijri'),
            ]
        ];

        $viewModes = [
            [
                'value' => 'grid',
                'label' => __('Grid'),
            ],
            [
                'value' => 'list',
                'label' => __('List'),
            ]
        ];

        return Inertia::render('Profile/Edit', [
            'hotel' => $hotel,
            'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
            'calendars' => $calendars,
            'viewModes' => $viewModes,
            'reservationTypes' => config('custom.reservations.types'),
            'terms' => $settings->reservation_lease_terms,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Profile has been updated.')]);
    }
}