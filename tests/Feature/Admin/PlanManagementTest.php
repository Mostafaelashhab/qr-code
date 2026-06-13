<?php

use App\Enums\Feature;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
});

it('creates a plan with a generated slug', function () {
    $this->actingAs($this->admin)->post(route('admin.plans.store'), [
        'name' => 'Premium Plan',
        'price' => 750,
        'billing_period' => 'monthly',
        'max_users' => 25,
        'features' => [Feature::Attendance->value, Feature::Reports->value],
        'is_active' => true,
    ])->assertRedirect(route('admin.plans.index'));

    $plan = Plan::firstWhere('name', 'Premium Plan');
    expect($plan->slug)->toBe('premium-plan')
        ->and($plan->features)->toBe([Feature::Attendance->value, Feature::Reports->value]);
});

it('updates a plan', function () {
    $plan = Plan::factory()->create();

    $this->actingAs($this->admin)->put(route('admin.plans.update', $plan), [
        'name' => 'Updated Plan',
        'price' => 999,
        'billing_period' => 'yearly',
        'is_active' => false,
    ])->assertRedirect(route('admin.plans.index'));

    expect($plan->fresh())->name->toBe('Updated Plan')->is_active->toBeFalse();
});

it('blocks deleting a plan that has subscriptions', function () {
    $plan = Plan::factory()->create();
    Subscription::factory()->create(['plan_id' => $plan->id]);

    $this->actingAs($this->admin)->delete(route('admin.plans.destroy', $plan))->assertForbidden();

    expect(Plan::find($plan->id))->not->toBeNull();
});
