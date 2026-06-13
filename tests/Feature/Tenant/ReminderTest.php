<?php

use App\Enums\AttendanceStatus;
use App\Enums\SmsType;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\SmsMessage;
use App\Models\Student;

it('sends absence reminders only to absent students with a guardian phone', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $session = AttendanceSession::factory()->create(['client_id' => $client->id, 'group_id' => $group->id]);

    $absentWithPhone = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0100']);
    $absentNoPhone = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => null]);
    $present = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0102']);

    Attendance::factory()->create(['client_id' => $client->id, 'attendance_session_id' => $session->id, 'student_id' => $absentWithPhone->id, 'status' => AttendanceStatus::Absent]);
    Attendance::factory()->create(['client_id' => $client->id, 'attendance_session_id' => $session->id, 'student_id' => $absentNoPhone->id, 'status' => AttendanceStatus::Absent]);
    Attendance::factory()->create(['client_id' => $client->id, 'attendance_session_id' => $session->id, 'student_id' => $present->id, 'status' => AttendanceStatus::Present]);

    $this->actingAs($admin)->post(route('tenant.reminders.absence', $session))->assertRedirect();

    $messages = SmsMessage::withoutGlobalScopes()->get();
    expect($messages)->toHaveCount(1)
        ->and($messages->first()->student_id)->toBe($absentWithPhone->id)
        ->and($messages->first()->type)->toBe(SmsType::Absence);
});

it('sends payment reminders to unpaid enrolled students', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $month = now()->format('Y-m');

    $paid = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0100']);
    $unpaid = Student::factory()->create(['client_id' => $client->id, 'guardian_phone' => '0101']);

    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $paid->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $unpaid->id]);
    Payment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $paid->id, 'for_month' => $month]);

    $this->actingAs($admin)->post(route('tenant.reminders.payment'), [
        'group_id' => $group->id,
        'month' => $month,
    ])->assertRedirect();

    $messages = SmsMessage::withoutGlobalScopes()->where('type', SmsType::PaymentDue)->get();
    expect($messages)->toHaveCount(1)
        ->and($messages->first()->student_id)->toBe($unpaid->id);
});

it('shows only the tenant own messages in the outbox', function () {
    [$client, $admin] = tenantWithAdmin();
    SmsMessage::factory()->create(['client_id' => $client->id, 'body' => 'My Message']);
    SmsMessage::factory()->create(['body' => 'Foreign Message']);

    $this->actingAs($admin)->get(route('tenant.messages.index'))
        ->assertOk()
        ->assertSee('My Message')
        ->assertDontSee('Foreign Message');
});
