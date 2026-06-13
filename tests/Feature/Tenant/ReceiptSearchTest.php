<?php

use App\Models\Payment;
use App\Models\Student;

it('renders a printable payment receipt', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id, 'name' => 'Receipt Pupil']);
    $payment = Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 275]);

    $this->actingAs($admin)->get(route('tenant.payments.receipt', $payment))
        ->assertOk()
        ->assertSee('Receipt Pupil')
        ->assertSee('275.00');
});

it('returns search results for students and groups', function () {
    [$client, $admin] = tenantWithAdmin();
    Student::factory()->create(['client_id' => $client->id, 'name' => 'Findme Student']);
    Student::factory()->create(['client_id' => $client->id, 'name' => 'Other Person']);

    $this->actingAs($admin)->get(route('tenant.search', ['q' => 'Findme']))
        ->assertOk()
        ->assertSee('Findme Student')
        ->assertDontSee('Other Person');
});

it('returns 404 for a payment receipt from another tenant', function () {
    [, $admin] = tenantWithAdmin();
    $foreign = Payment::factory()->create();

    $this->actingAs($admin)->get(route('tenant.payments.receipt', $foreign))->assertNotFound();
});
