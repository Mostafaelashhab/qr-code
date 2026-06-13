<?php

use App\Enums\Feature;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

/**
 * Active tenant whose plan grants exactly the given features.
 *
 * @param  array<int, Feature>  $features
 * @return array{0: Client, 1: User}
 */
function tenantWithFeatures(array $features): array
{
    $plan = Plan::factory()->features($features)->create();
    $client = Client::factory()->create();
    Subscription::factory()->active()->create(['client_id' => $client->id, 'plan_id' => $plan->id]);
    $admin = User::factory()->clientAdmin($client)->create();

    return [$client, $admin];
}

it('allows a gated module when the plan includes the feature', function () {
    [, $admin] = tenantWithFeatures([Feature::Payments]);

    $this->actingAs($admin)->get(route('tenant.payments.index'))->assertOk();
});

it('blocks a gated module when the plan excludes the feature', function () {
    [, $admin] = tenantWithFeatures([Feature::Attendance]); // no payments

    $this->actingAs($admin)->get(route('tenant.payments.index'))->assertForbidden();
});

it('blocks every excluded module', function () {
    [, $admin] = tenantWithFeatures([]); // bare plan

    foreach ([
        'tenant.reports.index',
        'tenant.expenses.index',
        'tenant.timetable.index',
        'tenant.messages.index',
    ] as $route) {
        $this->actingAs($admin)->get(route($route))->assertForbidden();
    }
});

it('still allows core modules regardless of features', function () {
    [, $admin] = tenantWithFeatures([]);

    $this->actingAs($admin)->get(route('tenant.students.index'))->assertOk();
    $this->actingAs($admin)->get(route('tenant.groups.index'))->assertOk();
});

it('hides nav links for excluded features', function () {
    [, $admin] = tenantWithFeatures([Feature::Payments]);

    $response = $this->actingAs($admin)->get(route('tenant.dashboard'));
    $response->assertSee(route('tenant.payments.index'), false);
    $response->assertDontSee(route('tenant.reports.index'), false);
});

it('exposes feature checks on the client model', function () {
    [$client] = tenantWithFeatures([Feature::Exams]);

    expect($client->hasFeature(Feature::Exams))->toBeTrue()
        ->and($client->hasFeature(Feature::Timetable))->toBeFalse();
});

it('validates plan feature keys when a super admin creates a plan', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->post(route('admin.plans.store'), [
        'name' => 'Bad Plan',
        'price' => 100,
        'billing_period' => 'monthly',
        'features' => ['attendance', 'not-a-feature'],
    ])->assertSessionHasErrors('features.1');
});

it('stores selected features on a new plan', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->post(route('admin.plans.store'), [
        'name' => 'Feature Plan',
        'price' => 100,
        'billing_period' => 'monthly',
        'features' => [Feature::Exams->value, Feature::Reports->value],
    ])->assertRedirect();

    $plan = Plan::firstWhere('name', 'Feature Plan');
    expect($plan->includesFeature(Feature::Exams))->toBeTrue()
        ->and($plan->includesFeature(Feature::Payments))->toBeFalse();
});
