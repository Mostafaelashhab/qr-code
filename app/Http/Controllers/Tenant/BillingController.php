<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\PaymentChannel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSubscriptionPaymentRequest;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    /**
     * Billing hub: current subscription, available plans, payment instructions
     * and the center's submitted payment requests.
     */
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        return view('tenant.billing.index', [
            'client' => $client,
            'current' => $client->activeSubscription()?->loadMissing('plan'),
            'plans' => Plan::active()->orderBy('sort_order')->orderBy('price')->get(),
            'channels' => PaymentChannel::cases(),
            'payments' => SubscriptionPayment::with('plan')->latest()->get(),
        ]);
    }

    public function store(StoreSubscriptionPaymentRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('receipt');

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        SubscriptionPayment::create($data);

        return redirect()->route('tenant.billing.index')->with('status', __('messages.payment_submitted'));
    }

    public function receipt(SubscriptionPayment $payment): View
    {
        $payment->load(['plan', 'client']);

        return view('tenant.billing.receipt', compact('payment'));
    }
}
