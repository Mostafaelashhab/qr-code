<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

/**
 * Create an active tenant with a client admin signed in.
 */
function activeTenantAdmin(): array
{
    $client = Client::factory()->create();
    Subscription::factory()->active()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
    ]);
    $admin = User::factory()->clientAdmin($client)->create();

    return [$client, $admin];
}

it('lists only users from the same client', function () {
    [$client, $admin] = activeTenantAdmin();
    User::factory()->clientUser($client)->create(['name' => 'Mine']);
    User::factory()->clientUser()->create(['name' => 'Theirs']);

    $this->actingAs($admin)->get(route('tenant.users.index'))
        ->assertOk()
        ->assertSee('Mine')
        ->assertDontSee('Theirs');
});

it('creates a user under the current client', function () {
    [$client, $admin] = activeTenantAdmin();

    $this->actingAs($admin)->post(route('tenant.users.store'), [
        'name' => 'New Staff',
        'email' => 'staff@center.test',
        'role' => UserRole::ClientUser->value,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('tenant.users.index'));

    $user = User::firstWhere('email', 'staff@center.test');
    expect($user->client_id)->toBe($client->id)
        ->and($user->role)->toBe(UserRole::ClientUser);
});

it('returns 404 when editing a user from another client', function () {
    [, $admin] = activeTenantAdmin();
    $foreignUser = User::factory()->clientUser()->create();

    $this->actingAs($admin)->get(route('tenant.users.edit', $foreignUser))->assertNotFound();
});

it('forbids a regular staff user from managing users', function () {
    [$client] = activeTenantAdmin();
    $staff = User::factory()->clientUser($client)->create();

    $this->actingAs($staff)->get(route('tenant.users.index'))->assertForbidden();
});
