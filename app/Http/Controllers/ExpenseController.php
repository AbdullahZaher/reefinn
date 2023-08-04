<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(ExpenseRequest $request)
    {
        Expense::create($request->validated());

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Expense has been added.')]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Expense has been deleted.')]);
    }
}