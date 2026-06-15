<?php

use App\Enums\AttendanceStatus;
use App\Enums\MessageChannel;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\SmsMessage;
use App\Models\Student;

it('skips guardians who opted out of absence reminders', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $session = AttendanceSession::factory()->create(['client_id' => $client->id, 'group_id' => $group->id]);

    $optedIn = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0100', 'reminders_opt_out' => false]);
    $optedOut = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0101', 'reminders_opt_out' => true]);

    foreach ([$optedIn, $optedOut] as $student) {
        Attendance::factory()->create([
            'client_id' => $client->id,
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => AttendanceStatus::Absent,
        ]);
    }

    $this->actingAs($admin)->post(route('tenant.reminders.absence', $session))->assertRedirect();

    $messages = SmsMessage::withoutGlobalScopes()->get();
    expect($messages)->toHaveCount(1)
        ->and($messages->first()->student_id)->toBe($optedIn->id)
        ->and($messages->first()->channel)->toBe(MessageChannel::WhatsApp);
});

it('skips opted-out guardians for payment reminders', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $month = now()->format('Y-m');

    $optedIn = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0100', 'reminders_opt_out' => false]);
    $optedOut = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0101', 'reminders_opt_out' => true]);

    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $optedIn->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $optedOut->id]);

    $this->actingAs($admin)->post(route('tenant.reminders.payment'), [
        'group_id' => $group->id,
        'month' => $month,
    ])->assertRedirect();

    $messages = SmsMessage::withoutGlobalScopes()->get();
    expect($messages)->toHaveCount(1)
        ->and($messages->first()->student_id)->toBe($optedIn->id);
});

it('lets a guardian opt out from the portal', function () {
    [$client] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id, 'reminders_opt_out' => false]);

    $this->post(route('portal.reminders', $student->guardian_token))
        ->assertRedirect(route('portal.show', $student->guardian_token));

    expect($student->fresh()->reminders_opt_out)->toBeTrue();
});
