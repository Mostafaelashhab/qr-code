<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Carbon;

class StartTrial
{
    /**
     * Give a brand-new client a free trial on the cheapest active plan.
     * Returns the created trial subscription, or null when no plan is available.
     */
    public function execute(Client $client, ?int $days = null): ?Subscription
    {
        $plan = Plan::active()->orderBy('price')->orderBy('sort_order')->first();

        if ($plan === null) {
            return null;
        }

        $days ??= (int) config('billing.trial_days', 14);

        return $client->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'price' => 0,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays($days),
            'notes' => __('billing.trial_note'),
        ]);
    }
}
