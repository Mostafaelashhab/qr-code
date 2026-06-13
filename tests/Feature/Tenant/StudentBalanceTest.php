<?php

use App\Models\Charge;
use App\Models\Payment;
use App\Models\Student;

it('computes balance as charges (after discount) minus payments', function () {
    [$client] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);

    Charge::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 400, 'discount' => 50]);
    Payment::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 200]);

    expect($student->totalCharged())->toBe(350.0)
        ->and($student->totalPaid())->toBe(200.0)
        ->and($student->balance())->toBe(150.0);
});

it('adds a charge from the student profile', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.charges.store'), [
        'student_id' => $student->id,
        'title' => 'Books',
        'amount' => 120,
        'discount' => 20,
        'for_month' => now()->format('Y-m'),
    ])->assertRedirect(route('tenant.students.show', $student));

    $charge = Charge::withoutGlobalScopes()->first();
    expect($charge->client_id)->toBe($client->id)
        ->and($charge->netAmount())->toBe(100.0);
});

it('rejects a discount greater than the amount', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.charges.store'), [
        'student_id' => $student->id,
        'title' => 'Bad',
        'amount' => 100,
        'discount' => 150,
    ])->assertSessionHasErrors('discount');
});

it('shows the outstanding balance on the student profile', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id, 'name' => 'Balance Pupil']);
    Charge::factory()->create(['client_id' => $client->id, 'student_id' => $student->id, 'amount' => 300, 'discount' => 0]);

    $this->actingAs($admin)->get(route('tenant.students.show', $student))
        ->assertOk()
        ->assertSee(__('ui.balance'));
});

it('blocks charges when the plan lacks the payments feature', function () {
    [$client, $admin] = tenantWithFeatures([\App\Enums\Feature::Attendance]); // no payments
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.charges.store'), [
        'student_id' => $student->id,
        'title' => 'X',
        'amount' => 100,
    ])->assertForbidden();
});
