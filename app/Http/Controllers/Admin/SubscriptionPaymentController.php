<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Subscriptions\StartSubscription;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = SubscriptionPayment::query()
            ->with(['client', 'plan', 'reviewer'])
            ->when($request->enum('status', PaymentRequestStatus::class), function ($query, PaymentRequestStatus $status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscription_payments.index', compact('payments'));
    }

    /**
     * Approve a payment and activate the matching subscription.
     */
    public function approve(SubscriptionPayment $payment, StartSubscription $action): RedirectResponse
    {
        abort_unless($payment->isPending(), 422);

        $action->execute(
            client: $payment->client,
            plan: $payment->plan,
            price: (float) $payment->amount,
            notes: __('billing.approved_via', ['ref' => $payment->reference]),
        );

        $payment->update([
            'status' => PaymentRequestStatus::Approved,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('status', __('messages.payment_approved'));
    }

    public function reject(Request $request, SubscriptionPayment $payment): RedirectResponse
    {
        abort_unless($payment->isPending(), 422);

        $request->validate(['review_note' => ['nullable', 'string', 'max:255']]);

        $payment->update([
            'status' => PaymentRequestStatus::Rejected,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $request->input('review_note'),
        ]);

        return back()->with('status', __('messages.payment_rejected'));
    }
}
