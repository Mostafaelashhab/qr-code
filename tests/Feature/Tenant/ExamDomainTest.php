<?php

use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Group;
use App\Models\Student;

it('creates an exam under a group', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.exams.store', $group), [
        'name' => 'Midterm',
        'exam_date' => now()->toDateString(),
        'max_score' => 50,
    ])->assertRedirect();

    expect(Exam::withoutGlobalScopes()->where('group_id', $group->id)->first()?->name)->toBe('Midterm');
});

it('saves grades for enrolled students and clamps to max score', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);
    $exam = Exam::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'max_score' => 50]);

    $this->actingAs($admin)->post(route('tenant.grades.store', $exam), [
        'scores' => [$student->id => 80], // above max, should clamp to 50
    ])->assertRedirect();

    expect((float) $exam->grades()->first()->score)->toBe(50.0);
});

it('returns 404 for an exam from another tenant', function () {
    [, $admin] = tenantWithAdmin();
    $foreign = Exam::factory()->create();

    $this->actingAs($admin)->get(route('tenant.exams.show', $foreign))->assertNotFound();
});
