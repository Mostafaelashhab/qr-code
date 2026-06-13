<?php

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

it('shows platform analytics to the super admin', function () {
    $admin = User::factory()->superAdmin()->create();
    $plan = Plan::factory()->create(['name' => 'Growth Plan', 'sort_order' => 1]);

    Subscription::factory()->create([
        'client_id' => Client::factory()->create()->id,
        'plan_id' => $plan->id,
        'status' => SubscriptionStatus::Active,
        'price' => 499,
    ]);

    $this->actingAs($admin)->get(route('admin.reports'))
        ->assertOk()
        ->assertSee('Growth Plan')
        ->assertSee('499.00');
});

it('forbids tenant users from the platform analytics', function () {
    $user = User::factory()->clientAdmin()->create();

    $this->actingAs($user)->get(route('admin.reports'))->assertForbidden();
});
