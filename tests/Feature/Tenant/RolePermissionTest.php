<?php

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;

/**
 * An active tenant with a signed-in admin, plus a staff member holding the
 * given permissions via a custom role.
 *
 * @param  array<int, Permission>  $permissions
 * @return array{0: Client, 1: User, 2: User}
 */
function tenantWithStaff(array $permissions = []): array
{
    $client = Client::factory()->create();
    Subscription::factory()->active()->create([
        'client_id' => $client->id,
        'plan_id' => Plan::factory()->create()->id,
    ]);
    $admin = User::factory()->clientAdmin($client)->create();

    $role = Role::factory()->for($client)->create();
    $role->syncPermissions($permissions);
    $staff = User::factory()->clientUser($client)->create(['role_id' => $role->id]);

    return [$client, $admin, $staff];
}

it('lets a center admin manage roles', function () {
    [$client, $admin] = tenantWithStaff();

    $this->actingAs($admin)->post(route('tenant.roles.store'), [
        'name' => 'Receptionist',
        'permissions' => [Permission::Students->value, Permission::Payments->value],
    ])->assertRedirect(route('tenant.roles.index'));

    $role = Role::where('client_id', $client->id)->where('name', 'Receptionist')->firstOrFail();
    expect($role->hasPermission(Permission::Students))->toBeTrue()
        ->and($role->hasPermission(Permission::Payments))->toBeTrue()
        ->and($role->hasPermission(Permission::Reports))->toBeFalse();
});

it('blocks staff from a section their role lacks', function () {
    [, , $staff] = tenantWithStaff(permissions: [Permission::Groups]);

    $this->actingAs($staff)->get(route('tenant.students.index'))->assertForbidden();
});

it('allows staff into a section their role grants', function () {
    [, , $staff] = tenantWithStaff(permissions: [Permission::Students]);

    $this->actingAs($staff)->get(route('tenant.students.index'))->assertOk();
});

it('grants a center admin every permission implicitly', function () {
    [, $admin] = tenantWithStaff();

    expect($admin->hasPermission(Permission::Payments))->toBeTrue()
        ->and($admin->hasPermission(Permission::Reports))->toBeTrue();
});

it('forbids staff from managing roles', function () {
    [, , $staff] = tenantWithStaff(permissions: [Permission::Students]);

    $this->actingAs($staff)->get(route('tenant.roles.index'))->assertForbidden();
});

it('refuses to delete a role that still has staff', function () {
    [$client, $admin, $staff] = tenantWithStaff(permissions: [Permission::Students]);
    $role = $staff->staffRole;

    $this->actingAs($admin)->delete(route('tenant.roles.destroy', $role))
        ->assertSessionHasErrors('delete');

    expect(Role::find($role->id))->not->toBeNull();
});

it('prevents demoting the last center admin', function () {
    [$client, $admin] = tenantWithStaff();
    $role = Role::factory()->for($client)->create();

    $this->actingAs($admin)->put(route('tenant.users.update', $admin), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role_ref' => (string) $role->id,
    ])->assertSessionHasErrors('role_ref');

    expect($admin->fresh()->role)->toBe(UserRole::ClientAdmin);
});
