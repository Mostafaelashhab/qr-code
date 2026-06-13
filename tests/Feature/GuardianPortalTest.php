<?php

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Charge;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;

it('auto-generates a guardian token for every student', function () {
    $student = Student::factory()->create();

    expect($student->guardian_token)->not->toBeNull();
});

it('shows the public parent portal by token without authentication', function () {
    $client = \App\Models\Client::factory()->create(['name' => 'Noor Center']);
    $student = Student::factory()->create(['client_id' => $client->id, 'name' => 'Child One']);
    $group = Group::factory()->create(['client_id' => $client->id, 'name' => 'Math A']);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);

    $session = AttendanceSession::factory()->create(['client_id' => $client->id, 'group_id' => $group->id]);
    Attendance::factory()->create(['client_id' => $client->id, 'attendance_session_id' => $session->id, 'student_id' => $student->id, 'status' => AttendanceStatus::Present]);
    Charge::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 300, 'discount' => 0]);

    $this->get(route('portal.show', $student->guardian_token))
        ->assertOk()
        ->assertSee('Child One')
        ->assertSee('Noor Center')
        ->assertSee('Math A');
});

it('returns 404 for an unknown portal token', function () {
    $this->get(route('portal.show', 'nope-token'))->assertNotFound();
});

it('regenerating the token invalidates the old link', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);
    $old = $student->guardian_token;

    $this->actingAs($admin)->post(route('tenant.students.portal.regenerate', $student))->assertRedirect();

    $student->refresh();
    expect($student->guardian_token)->not->toBe($old);

    $this->get(route('portal.show', $old))->assertNotFound();
    $this->get(route('portal.show', $student->guardian_token))->assertOk();
});
