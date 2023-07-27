<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Alkoumi\LaravelHijriDate\Hijri;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            App::setLocale($locale);
        } else {
            $locale = 'ar';
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        Carbon::setLocale($locale);
        Hijri::setLang($locale);

        return $next($request);
    }
}
