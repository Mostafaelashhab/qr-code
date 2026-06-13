<?php

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

it('routes a super admin to the admin dashboard', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
});

it('routes a tenant user to the tenant dashboard', function () {
    $client = Client::factory()->create();
    $client->subscriptions()->save(Subscription::factory()->active()->make(['plan_id' => Plan::factory()->create()->id]));
    $user = User::factory()->clientAdmin($client)->create();

    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('tenant.dashboard'));
});

it('forbids a tenant user from the admin area', function () {
    $user = User::factory()->clientAdmin()->create();

    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
});

it('forbids a super admin from the tenant area', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->get(route('tenant.dashboard'))->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});
