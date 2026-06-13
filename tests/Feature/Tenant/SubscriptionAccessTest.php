<?php

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

it('redirects tenant users without an active subscription to the inactive notice', function () {
    $client = Client::factory()->create();
    $user = User::factory()->clientAdmin($client)->create();

    $this->actingAs($user)->get(route('tenant.dashboard'))
        ->assertRedirect(route('tenant.subscription.inactive'));
});

it('allows tenant users with an active subscription', function () {
    $client = Client::factory()->create();
    Subscription::factory()->active()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
    ]);
    $user = User::factory()->clientAdmin($client)->create();

    $this->actingAs($user)->get(route('tenant.dashboard'))->assertOk();
});

it('blocks users whose client account is disabled', function () {
    $client = Client::factory()->inactive()->create();
    Subscription::factory()->active()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
    ]);
    $user = User::factory()->clientAdmin($client)->create();

    $this->actingAs($user)->get(route('tenant.dashboard'))->assertForbidden();
});
