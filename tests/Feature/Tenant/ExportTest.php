<?php

use App\Models\Payment;
use App\Models\Student;

it('exports tenant students as CSV', function () {
    [$client, $admin] = tenantWithAdmin();
    Student::factory()->create(['client_id' => $client->id, 'name' => 'Exported Pupil']);

    $response = $this->actingAs($admin)->get(route('tenant.exports.students'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
    expect($response->streamedContent())->toContain('Exported Pupil');
});

it('exports tenant payments as CSV', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id, 'name' => 'Paying Pupil']);
    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 250]);

    $response = $this->actingAs($admin)->get(route('tenant.exports.payments'));

    $response->assertOk();
    expect($response->streamedContent())->toContain('Paying Pupil')->toContain('250');
});
