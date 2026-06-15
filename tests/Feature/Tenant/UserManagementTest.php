<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Role;
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

it('creates a staff user with a custom role under the current client', function () {
    [$client, $admin] = activeTenantAdmin();
    $role = Role::factory()->for($client)->create(['name' => 'Receptionist']);

    $this->actingAs($admin)->post(route('tenant.users.store'), [
        'name' => 'New Staff',
        'email' => 'staff@center.test',
        'role_ref' => (string) $role->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('tenant.users.index'));

    $user = User::firstWhere('email', 'staff@center.test');
    expect($user->client_id)->toBe($client->id)
        ->and($user->role)->toBe(UserRole::ClientUser)
        ->and($user->role_id)->toBe($role->id);
});

it('creates a center admin when "admin" is chosen', function () {
    [$client, $admin] = activeTenantAdmin();

    $this->actingAs($admin)->post(route('tenant.users.store'), [
        'name' => 'Co Admin',
        'email' => 'coadmin@center.test',
        'role_ref' => 'admin',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('tenant.users.index'));

    $user = User::firstWhere('email', 'coadmin@center.test');
    expect($user->role)->toBe(UserRole::ClientAdmin)
        ->and($user->role_id)->toBeNull();
});

it('rejects a role belonging to another center', function () {
    [, $admin] = activeTenantAdmin();
    $foreignRole = Role::factory()->create();

    $this->actingAs($admin)->post(route('tenant.users.store'), [
        'name' => 'New Staff',
        'email' => 'staff2@center.test',
        'role_ref' => (string) $foreignRole->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('role_ref');
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
