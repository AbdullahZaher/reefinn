<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Resources\ExpenseResource;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->can('show expenses'))
            $expenses = Expense::query()
                                ->get()
                                ->sortBy('date', SORT_REGULAR, true);
        else $expenses = [];

        return Inertia::render('Finance', [
            'expenses' => ExpenseResource::collection($expenses),
        ]);
    }
}
