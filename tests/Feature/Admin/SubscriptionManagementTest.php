<?php

use App\Actions\Subscriptions\StartSubscription;
use App\Enums\BillingPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
});

it('starts a subscription for a client', function () {
    $client = Client::factory()->create();
    $plan = Plan::factory()->create(['billing_period' => BillingPeriod::Monthly, 'price' => 300]);

    $this->actingAs($this->admin)->post(route('admin.subscriptions.store'), [
        'client_id' => $client->id,
        'plan_id' => $plan->id,
    ])->assertRedirect(route('admin.clients.show', $client));

    $subscription = $client->subscriptions()->first();
    expect($subscription->status)->toBe(SubscriptionStatus::Active)
        ->and((float) $subscription->price)->toBe(300.0)
        ->and($client->fresh()->hasActiveSubscription())->toBeTrue();
});

it('cancels any previous active subscription when starting a new one', function () {
    $client = Client::factory()->create();
    $plan = Plan::factory()->create();

    $action = app(StartSubscription::class);
    $first = $action->execute($client, $plan);
    $action->execute($client, $plan);

    expect($first->fresh()->status)->toBe(SubscriptionStatus::Cancelled)
        ->and($client->subscriptions()->where('status', SubscriptionStatus::Active)->count())->toBe(1);
});

it('renews a subscription by extending its end date', function () {
    $client = Client::factory()->create();
    $plan = Plan::factory()->create(['billing_period' => BillingPeriod::Monthly]);
    $subscription = app(StartSubscription::class)->execute($client, $plan);

    $originalEnd = $subscription->ends_at;

    $this->actingAs($this->admin)->post(route('admin.subscriptions.renew', $subscription))->assertRedirect();

    expect($subscription->fresh()->ends_at->greaterThan($originalEnd))->toBeTrue();
});
