<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function store(TaxRequest $request)
    {
        Tax::create($request->validated());

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Tax has been added.')]);
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Tax has been deleted.')]);
    }
}