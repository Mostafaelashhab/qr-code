<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Support\Carbon;

class RenewSubscription
{
    /**
     * Extend a subscription by one billing cycle of its plan.
     *
     * Renewing from a still-valid end date stacks the new cycle on top; renewing an
     * already-expired subscription starts a fresh cycle from now.
     */
    public function execute(Subscription $subscription): Subscription
    {
        $from = $subscription->ends_at && $subscription->ends_at->isFuture()
            ? $subscription->ends_at
            : Carbon::now();

        $subscription->update([
            'status' => SubscriptionStatus::Active,
            'ends_at' => $subscription->plan->billing_period->advance($from),
            'cancelled_at' => null,
        ]);

        return $subscription;
    }
}
