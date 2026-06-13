<?php

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;

it('reports monthly income, expenses and net profit', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);

    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 1000, 'paid_at' => now()]);
    Expense::factory()->create(['client_id' => $client->id, 'amount' => 300, 'spent_at' => now()]);

    $this->actingAs($admin)->get(route('tenant.reports.financial', ['month' => now()->format('Y-m')]))
        ->assertOk()
        ->assertSee('1,000.00')
        ->assertSee('300.00')
        ->assertSee('700.00');
});

it('splits paid and unpaid students in the collection report', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id, 'monthly_fee' => 200]);
    $paidStudent = Student::factory()->create(['client_id' => $client->id, 'name' => 'Paid Pupil']);
    $unpaidStudent = Student::factory()->create(['client_id' => $client->id, 'name' => 'Unpaid Pupil']);

    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $paidStudent->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $unpaidStudent->id]);

    Payment::factory()->create([
        'client_id' => $client->id,
        'student_id' => $paidStudent->id,
        'group_id' => $group->id,
        'for_month' => now()->format('Y-m'),
    ]);

    $this->actingAs($admin)->get(route('tenant.reports.collection', ['group_id' => $group->id, 'month' => now()->format('Y-m')]))
        ->assertOk()
        ->assertSee('Paid Pupil')
        ->assertSee('Unpaid Pupil')
        ->assertSee(__('ui.collected'), false);
});

it('computes attendance rate per student', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id, 'name' => 'Rate Student']);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);

    $session = AttendanceSession::factory()->create([
        'client_id' => $client->id,
        'group_id' => $group->id,
        'session_date' => now(),
    ]);
    Attendance::factory()->create([
        'client_id' => $client->id,
        'attendance_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
    ]);

    $this->actingAs($admin)->get(route('tenant.reports.attendance', ['group_id' => $group->id, 'month' => now()->format('Y-m')]))
        ->assertOk()
        ->assertSee('Rate Student')
        ->assertSee('100%');
});
