<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StartSubscription
{
    /**
     * Activate a new subscription for a client on the given plan.
     *
     * Any previously active subscription for the client is marked cancelled so a
     * client only ever has one active subscription at a time.
     */
    public function execute(
        Client $client,
        Plan $plan,
        ?Carbon $startsAt = null,
        ?float $price = null,
        ?string $notes = null,
    ): Subscription {
        $startsAt ??= Carbon::now();

        return DB::transaction(function () use ($client, $plan, $startsAt, $price, $notes): Subscription {
            $client->subscriptions()
                ->where('status', SubscriptionStatus::Active)
                ->update([
                    'status' => SubscriptionStatus::Cancelled,
                    'cancelled_at' => Carbon::now(),
                ]);

            return $client->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active,
                'price' => $price ?? $plan->price,
                'starts_at' => $startsAt,
                'ends_at' => $plan->billing_period->advance($startsAt),
                'notes' => $notes,
            ]);
        });
    }
}
