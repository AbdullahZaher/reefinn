<?php

namespace App\Http\Middleware;

use App\Models\Tax;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::user() ? Auth::user()->load('permissions') : null;

        return array_merge(parent::share($request), [
            'app' => [
                'tax_percentage' => Tax::sum('percentage'),
            ],
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'timezone' => $user->timezone,
                    'calendar' => $user->calendar,
                    'view_mode' => $user->view_mode,
                    'max_discount' => $user->max_discount,
                    'can' => $user->permissions->pluck('name'),
                ] : null,
            ],
            'flash' =>  Session::get('toast') ? array_merge(Session::get('toast'), ['id' => uniqid()]) : null,
            'locale' => [
                'lang' => Session::get('locale'),
                'dir' => Session::get('locale') == 'ar' ? 'rtl' : 'ltr',
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                    'params' => $request->query(),
                ]);
            },
        ]);
    }
}