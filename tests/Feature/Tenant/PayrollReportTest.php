<?php

use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;

it('computes teacher earnings as a share of collected revenue', function () {
    [$client, $admin] = tenantWithAdmin();
    $teacher = Teacher::factory()->create(['client_id' => $client->id, 'name' => 'Mr Payroll']);
    $group = Group::factory()->create(['client_id' => $client->id, 'teacher_id' => $teacher->id, 'teacher_share' => 50]);
    $student = Student::factory()->create(['client_id' => $client->id]);

    // 1000 collected this month -> teacher earns 50% = 500.
    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'group_id' => $group->id, 'amount' => 600, 'paid_at' => now()]);
    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'group_id' => $group->id, 'amount' => 400, 'paid_at' => now()]);
    // A payment outside the month must be ignored.
    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'group_id' => $group->id, 'amount' => 999, 'paid_at' => now()->subMonths(2)]);

    $this->actingAs($admin)->get(route('tenant.reports.payroll', ['month' => now()->format('Y-m')]))
        ->assertOk()
        ->assertSee('Mr Payroll')
        ->assertSee('500.00')
        ->assertSee('1,000.00');
});

it('validates teacher share between 0 and 100 when saving a group', function () {
    [$client, $admin] = tenantWithAdmin();
    $subject = \App\Models\Subject::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.groups.store'), [
        'name' => 'Bad Share',
        'subject_id' => $subject->id,
        'monthly_fee' => 100,
        'teacher_share' => 150,
    ])->assertSessionHasErrors('teacher_share');
});

it('blocks the payroll report without the reports feature', function () {
    [, $admin] = tenantWithFeatures([\App\Enums\Feature::Payments]); // no reports

    $this->actingAs($admin)->get(route('tenant.reports.payroll'))->assertForbidden();
});
