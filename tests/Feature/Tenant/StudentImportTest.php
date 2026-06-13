<?php

use App\Models\Client;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('imports students from a CSV file', function () {
    [$client, $admin] = tenantWithAdmin();

    $csv = "name,phone,guardian_phone,stage\n"
        ."Omar Hassan,0100,0111,Grade 10\n"
        ."Sara Adel,0102,0113,Grade 11\n";
    $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

    $this->actingAs($admin)
        ->post(route('tenant.students.import.store'), ['file' => $file])
        ->assertRedirect(route('tenant.students.index'));

    expect(Student::withoutGlobalScopes()->where('client_id', $client->id)->count())->toBe(2);
    $omar = Student::withoutGlobalScopes()->where('name', 'Omar Hassan')->first();
    expect($omar->stage)->toBe('Grade 10')->and($omar->qr_token)->not->toBeNull();
});

it('skips rows without a name', function () {
    [$client, $admin] = tenantWithAdmin();

    $csv = "name,phone\nValid Student,0100\n,0102\n";
    $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

    $this->actingAs($admin)->post(route('tenant.students.import.store'), ['file' => $file])->assertRedirect();

    expect(Student::withoutGlobalScopes()->where('client_id', $client->id)->count())->toBe(1);
});

it('rejects a file missing the name column', function () {
    [, $admin] = tenantWithAdmin();

    $file = UploadedFile::fake()->createWithContent('students.csv', "phone,stage\n0100,Grade 10\n");

    $this->actingAs($admin)->post(route('tenant.students.import.store'), ['file' => $file])
        ->assertSessionHasErrors('file');
});

it('stops importing once the plan student limit is reached', function () {
    $plan = Plan::factory()->create(['max_students' => 1]);
    $client = Client::factory()->create();
    Subscription::factory()->active()->create(['client_id' => $client->id, 'plan_id' => $plan->id]);
    $admin = User::factory()->clientAdmin($client)->create();

    $csv = "name\nFirst\nSecond\nThird\n";
    $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

    $this->actingAs($admin)->post(route('tenant.students.import.store'), ['file' => $file])->assertRedirect();

    expect(Student::withoutGlobalScopes()->where('client_id', $client->id)->count())->toBe(1);
});

it('downloads a CSV template', function () {
    [, $admin] = tenantWithAdmin();

    $response = $this->actingAs($admin)->get(route('tenant.students.import.template'));

    $response->assertOk();
    expect($response->streamedContent())->toContain('name')->toContain('guardian_phone');
});
