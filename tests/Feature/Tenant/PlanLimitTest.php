<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;

/**
 * Active tenant on a plan with explicit limits.
 *
 * @return array{0: Client, 1: User}
 */
function tenantOnPlan(?int $maxUsers, ?int $maxStudents): array
{
    $plan = Plan::factory()->create(['max_users' => $maxUsers, 'max_students' => $maxStudents]);
    $client = Client::factory()->create();
    Subscription::factory()->active()->create(['client_id' => $client->id, 'plan_id' => $plan->id]);
    $admin = User::factory()->clientAdmin($client)->create();

    return [$client, $admin];
}

it('blocks creating a student beyond the plan limit', function () {
    [$client, $admin] = tenantOnPlan(maxUsers: null, maxStudents: 1);
    Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.students.store'), ['name' => 'Over Limit'])
        ->assertSessionHasErrors('name');

    expect(Student::withoutGlobalScopes()->where('client_id', $client->id)->count())->toBe(1);
});

it('allows creating students under the plan limit', function () {
    [$client, $admin] = tenantOnPlan(maxUsers: null, maxStudents: 5);

    $this->actingAs($admin)->post(route('tenant.students.store'), ['name' => 'Within Limit'])
        ->assertSessionHasNoErrors();

    expect(Student::withoutGlobalScopes()->where('client_id', $client->id)->count())->toBe(1);
});

it('blocks creating a user beyond the plan limit', function () {
    // The admin itself counts as one user, so a limit of 1 is already reached.
    [, $admin] = tenantOnPlan(maxUsers: 1, maxStudents: null);

    $this->actingAs($admin)->post(route('tenant.users.store'), [
        'name' => 'Extra User',
        'email' => 'extra@center.test',
        'role' => UserRole::ClientUser->value,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('name');
});

it('treats a null limit as unlimited', function () {
    [$client, $admin] = tenantOnPlan(maxUsers: null, maxStudents: null);
    Student::factory()->count(3)->create(['client_id' => $client->id]);

    expect($client->canAddStudent())->toBeTrue()
        ->and($client->canAddUser())->toBeTrue();
});
