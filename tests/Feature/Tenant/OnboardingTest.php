<?php

use App\Models\Student;
use App\Models\Subject;

it('shows the getting-started checklist for an empty center', function () {
    [, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->get(route('tenant.dashboard'))
        ->assertOk()
        ->assertSee(__('ui.getting_started'))
        ->assertSee(__('ui.onboard_subject'));
});

it('hides the checklist once all steps are done', function () {
    [$client, $admin] = tenantWithAdmin();
    $subject = Subject::factory()->create(['client_id' => $client->id]);
    \App\Models\Teacher::factory()->create(['client_id' => $client->id]);
    \App\Models\Group::factory()->create(['client_id' => $client->id, 'subject_id' => $subject->id]);
    Student::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->get(route('tenant.dashboard'))
        ->assertOk()
        ->assertDontSee(__('ui.getting_started'));
});

it('shows testimonials on the landing page', function () {
    $this->get('/')->assertOk()->assertSee(__('landing.testimonials_title'));
});
