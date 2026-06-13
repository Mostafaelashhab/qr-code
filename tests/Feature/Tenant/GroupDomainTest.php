<?php

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;

it('creates a group within the tenant', function () {
    [$client, $admin] = tenantWithAdmin();
    $subject = Subject::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.groups.store'), [
        'name' => 'Algebra A',
        'subject_id' => $subject->id,
        'monthly_fee' => 250,
    ])->assertRedirect();

    expect(Group::withoutGlobalScopes()->where('name', 'Algebra A')->first()?->client_id)->toBe($client->id);
});

it('rejects a subject from another tenant when creating a group', function () {
    [, $admin] = tenantWithAdmin();
    $foreignSubject = Subject::factory()->create();

    $this->actingAs($admin)->post(route('tenant.groups.store'), [
        'name' => 'Bad Group',
        'subject_id' => $foreignSubject->id,
        'monthly_fee' => 100,
    ])->assertSessionHasErrors('subject_id');
});

it('enrolls a student into a group', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.groups.students.store', $group), [
        'student_id' => $student->id,
    ])->assertRedirect();

    expect($group->students()->count())->toBe(1);
});

it('prevents enrolling beyond capacity', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id, 'capacity' => 1]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.groups.students.store', $group), [
        'student_id' => $student->id,
    ])->assertSessionHasErrors('student_id');

    expect($group->students()->count())->toBe(1);
});

it('saves attendance for an enrolled roster', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);

    $this->actingAs($admin)->post(route('tenant.groups.attendance.store', $group), [
        'session_date' => now()->toDateString(),
        'statuses' => [$student->id => AttendanceStatus::Absent->value],
    ])->assertRedirect(route('tenant.groups.show', $group));

    $session = AttendanceSession::withoutGlobalScopes()->where('group_id', $group->id)->first();
    expect($session)->not->toBeNull()
        ->and($session->attendances()->where('student_id', $student->id)->first()->status)
        ->toBe(AttendanceStatus::Absent);
});

it('is idempotent when re-saving attendance for the same date', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);

    $payload = [
        'session_date' => now()->toDateString(),
        'statuses' => [$student->id => AttendanceStatus::Present->value],
    ];

    $this->actingAs($admin)->post(route('tenant.groups.attendance.store', $group), $payload);
    $this->actingAs($admin)->post(route('tenant.groups.attendance.store', $group), [
        ...$payload,
        'statuses' => [$student->id => AttendanceStatus::Late->value],
    ]);

    expect(AttendanceSession::withoutGlobalScopes()->where('group_id', $group->id)->count())->toBe(1)
        ->and($group->attendanceSessions()->first()->attendances()->count())->toBe(1);
});
