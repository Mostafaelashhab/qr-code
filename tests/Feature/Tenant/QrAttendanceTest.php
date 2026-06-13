<?php

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;

it('generates a unique qr token for every student', function () {
    $a = Student::factory()->create();
    $b = Student::factory()->create();

    expect($a->qr_token)->not->toBeNull()
        ->and($b->qr_token)->not->toBeNull()
        ->and($a->qr_token)->not->toBe($b->qr_token);
});

it('checks in an enrolled student by scanning their qr token', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);

    $this->actingAs($admin)
        ->postJson(route('tenant.groups.attendance.scan.store', $group), ['token' => $student->qr_token])
        ->assertOk()
        ->assertJson(['ok' => true]);

    $session = AttendanceSession::withoutGlobalScopes()->where('group_id', $group->id)->latest('id')->first();
    expect($session)->not->toBeNull()
        ->and($session->session_date->isToday())->toBeTrue()
        ->and($session->attendances()->where('student_id', $student->id)->first()->status)->toBe(AttendanceStatus::Present);
});

it('rejects an unknown qr token', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)
        ->postJson(route('tenant.groups.attendance.scan.store', $group), ['token' => 'nope'])
        ->assertStatus(404)
        ->assertJson(['ok' => false]);
});

it('rejects a student who is not enrolled in the group', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]); // not enrolled

    $this->actingAs($admin)
        ->postJson(route('tenant.groups.attendance.scan.store', $group), ['token' => $student->qr_token])
        ->assertStatus(422)
        ->assertJson(['ok' => false]);
});

it('does not check in a student from another tenant', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $foreign = Student::factory()->create(); // different tenant

    $this->actingAs($admin)
        ->postJson(route('tenant.groups.attendance.scan.store', $group), ['token' => $foreign->qr_token])
        ->assertStatus(404);
});

it('renders the qr scan page and printable cards', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    Student::factory()->create(['client_id' => $client->id, 'name' => 'Card Pupil']);

    $this->actingAs($admin)->get(route('tenant.groups.attendance.scan', $group))->assertOk();
    $this->actingAs($admin)->get(route('tenant.attendance.cards'))->assertOk()->assertSee('Card Pupil');
});

it('shows a single student QR card and the profile QR token', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id, 'name' => 'Solo Pupil']);

    $this->actingAs($admin)->get(route('tenant.students.card', $student))->assertOk()->assertSee('Solo Pupil');
    $this->actingAs($admin)->get(route('tenant.students.show', $student))->assertOk()->assertSee($student->qr_token, false);
});
