<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\TaxResource;
use App\Models\Tax;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->can('show expenses'))
            $expenses = Expense::query()
                                ->get()
                                ->sortBy('date', SORT_REGULAR, true);
        else $expenses = [];

        if ($request->user()->can('show taxes'))
            $taxes = Tax::query()
                        ->latest()
                        ->get();
        else $taxes = [];

        return Inertia::render('Finance', [
            'expenses' => ExpenseResource::collection($expenses),
            'taxes' => TaxResource::collection($taxes),
        ]);
    }
}