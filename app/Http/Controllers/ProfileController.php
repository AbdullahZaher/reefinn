<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
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

        return Inertia::render('Profile/Edit', [
            'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
            'calendars' => $calendars,
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

        return Redirect::route('profile.edit');
    }
}
