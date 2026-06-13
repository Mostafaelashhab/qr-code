<?php

use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Models\Student;

it('records a payment for a tenant student', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.payments.store'), [
        'student_id' => $student->id,
        'amount' => 300,
        'method' => PaymentMethod::Cash->value,
        'for_month' => now()->format('Y-m'),
        'paid_at' => now()->toDateString(),
    ])->assertRedirect(route('tenant.payments.index'));

    $payment = Payment::withoutGlobalScopes()->first();
    expect($payment->client_id)->toBe($client->id)
        ->and((float) $payment->amount)->toBe(300.0);
});

it('rejects a payment for a student from another tenant', function () {
    [, $admin] = tenantWithAdmin();
    $foreign = Student::factory()->create();

    $this->actingAs($admin)->post(route('tenant.payments.store'), [
        'student_id' => $foreign->id,
        'amount' => 100,
        'method' => PaymentMethod::Cash->value,
        'paid_at' => now()->toDateString(),
    ])->assertSessionHasErrors('student_id');
});

it('shows this month revenue on the payments index', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);
    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 500, 'paid_at' => now()]);

    $this->actingAs($admin)->get(route('tenant.payments.index'))
        ->assertOk()
        ->assertSee('500');
});
