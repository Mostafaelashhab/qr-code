<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StorePaymentRequest;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['student', 'group'])
            ->latest('paid_at')
            ->latest()
            ->paginate(20);

        $monthTotal = Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        return view('tenant.payments.index', compact('payments', 'monthTotal'));
    }

    public function create(Request $request): View
    {
        return view('tenant.payments.create', [
            'students' => Student::active()->orderBy('name')->get(),
            'groups' => Group::active()->orderBy('name')->get(),
            'selectedStudent' => $request->integer('student_id') ?: null,
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        Payment::create($request->validated());

        return redirect()->route('tenant.payments.index')->with('status', __('messages.payment_saved'));
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('tenant.payments.index')->with('status', __('messages.payment_deleted'));
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['student', 'group', 'client']);

        return view('tenant.payments.receipt', compact('payment'));
    }
}
