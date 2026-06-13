<?php

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;

it('marks past-due subscriptions as expired via the command', function () {
    $client = Client::factory()->create();
    $sub = Subscription::factory()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
        'status' => SubscriptionStatus::Active,
        'ends_at' => now()->subDay(),
    ]);

    $this->artisan('subscriptions:expire')->assertExitCode(0);

    expect($sub->fresh()->status)->toBe(SubscriptionStatus::Expired);
});

it('does not treat a past-due subscription as active even before the command runs', function () {
    $client = Client::factory()->create();
    Subscription::factory()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
        'status' => SubscriptionStatus::Active,
        'ends_at' => now()->subDay(),
    ]);

    expect($client->hasActiveSubscription())->toBeFalse();
});

it('keeps a future-dated active subscription active', function () {
    $client = Client::factory()->create();
    Subscription::factory()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
        'status' => SubscriptionStatus::Active,
        'ends_at' => now()->addWeek(),
    ]);

    expect($client->hasActiveSubscription())->toBeTrue();
});

it('starts a free trial when a new center registers', function () {
    Plan::factory()->create(['price' => 199, 'name' => 'Starter']);

    $this->post(route('register'), [
        'center_name' => 'Trial Center',
        'name' => 'Owner',
        'email' => 'owner@trial.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard'));

    $client = Client::firstWhere('name', 'Trial Center');
    expect($client->hasActiveSubscription())->toBeTrue()
        ->and((float) $client->activeSubscription()->price)->toBe(0.0);
});
