<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function __invoke($language)
    {
        if ($language == 'ar') Session::put('locale', 'ar');
        else Session::put('locale', 'en');

        return redirect()->route('dashboard');
    }
}
