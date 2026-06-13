<?php

use App\Models\Student;

it('lists students for the current tenant', function () {
    [$client, $admin] = tenantWithAdmin();
    Student::factory()->create(['client_id' => $client->id, 'name' => 'My Student']);
    Student::factory()->create(['name' => 'Other Tenant Student']); // different client

    $this->actingAs($admin)->get(route('tenant.students.index'))
        ->assertOk()
        ->assertSee('My Student')
        ->assertDontSee('Other Tenant Student');
});

it('creates a student and assigns the current client automatically', function () {
    [$client, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->post(route('tenant.students.store'), [
        'name' => 'Sara Ali',
        'phone' => '0100',
        'stage' => 'Grade 11',
    ])->assertRedirect(route('tenant.students.index'));

    $student = Student::withoutGlobalScopes()->firstWhere('name', 'Sara Ali');
    expect($student->client_id)->toBe($client->id);
});

it('returns 404 when viewing a student from another tenant', function () {
    [, $admin] = tenantWithAdmin();
    $foreign = Student::factory()->create(); // belongs to another client

    $this->actingAs($admin)->get(route('tenant.students.show', $foreign))->assertNotFound();
});

it('updates and deletes a student in the tenant', function () {
    [$client, $admin] = tenantWithAdmin();
    $student = Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->put(route('tenant.students.update', $student), [
        'name' => 'Updated Name',
    ])->assertRedirect();
    expect($student->fresh()->name)->toBe('Updated Name');

    $this->actingAs($admin)->delete(route('tenant.students.destroy', $student))->assertRedirect();
    expect(Student::withoutGlobalScopes()->find($student->id))->toBeNull();
});
