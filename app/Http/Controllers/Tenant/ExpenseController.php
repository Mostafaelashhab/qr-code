<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreExpenseRequest;
use App\Http\Requests\Tenant\UpdateExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::latest('spent_at')->latest()->paginate(20);

        $monthTotal = Expense::whereBetween('spent_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        return view('tenant.expenses.index', compact('expenses', 'monthTotal'));
    }

    public function create(): View
    {
        return view('tenant.expenses.create', ['expense' => new Expense]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        Expense::create($request->validated());

        return redirect()->route('tenant.expenses.index')->with('status', __('messages.expense_saved'));
    }

    public function edit(Expense $expense): View
    {
        return view('tenant.expenses.edit', compact('expense'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $expense->update($request->validated());

        return redirect()->route('tenant.expenses.index')->with('status', __('messages.expense_saved'));
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('tenant.expenses.index')->with('status', __('messages.expense_deleted'));
    }
}
