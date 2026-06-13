<?php

use App\Models\ActivityLog;
use App\Models\Student;

it('records an activity log when a student is created by a user', function () {
    [$client, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->post(route('tenant.students.store'), ['name' => 'Logged Pupil'])
        ->assertRedirect();

    $log = ActivityLog::where('client_id', $client->id)->where('action', 'created')->latest()->first();
    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($admin->id)
        ->and($log->description)->toContain('Logged Pupil');
});

it('does not log model changes made without an authenticated user', function () {
    $before = ActivityLog::count();
    Student::factory()->create(); // no acting user

    expect(ActivityLog::count())->toBe($before);
});

it('shows the activity log to the center admin scoped to its client', function () {
    [$client, $admin] = tenantWithAdmin();
    ActivityLog::create(['client_id' => $client->id, 'user_id' => $admin->id, 'action' => 'created', 'description' => 'Mine Activity']);
    ActivityLog::create(['client_id' => null, 'action' => 'created', 'description' => 'Foreign Activity']);

    $this->actingAs($admin)->get(route('tenant.activity.index'))
        ->assertOk()
        ->assertSee('Mine Activity')
        ->assertDontSee('Foreign Activity');
});
