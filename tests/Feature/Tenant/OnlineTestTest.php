<?php

use App\Enums\QuestionType;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Question;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestAttempt;

it('lets a center create a test and add an MCQ question', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.tests.store'), [
        'title' => 'Quiz A', 'group_id' => $group->id, 'duration_minutes' => 20,
    ])->assertRedirect();

    $test = Test::withoutGlobalScopes()->firstWhere('title', 'Quiz A');
    expect($test->token)->not->toBeNull();

    $this->actingAs($admin)->post(route('tenant.tests.questions.store', $test), [
        'body' => '2+2?', 'type' => 'mcq', 'points' => 1,
        'options' => ['3', '4', '5'], 'correct' => 1,
    ])->assertRedirect();

    $q = $test->questions()->withoutGlobalScopes()->first();
    expect($q->options()->withoutGlobalScopes()->where('is_correct', true)->first()->body)->toBe('4');
});

it('requires at least one question to publish', function () {
    [$client, $admin] = tenantWithAdmin();
    $test = Test::factory()->draft()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.tests.publish', $test))->assertSessionHasErrors('publish');
    expect($test->fresh()->is_published)->toBeFalse();
});

it('lets an enrolled student take a published test and auto-grades it', function () {
    [$client] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);

    $test = Test::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'is_published' => true]);
    $q = Question::factory()->create(['client_id' => $client->id, 'test_id' => $test->id, 'type' => QuestionType::Mcq, 'points' => 2]);
    $right = $q->options()->create(['client_id' => $client->id, 'body' => 'right', 'is_correct' => true]);
    $q->options()->create(['client_id' => $client->id, 'body' => 'wrong', 'is_correct' => false]);

    // public intro
    $this->get(route('test.show', $test->token))->assertOk()->assertSee($student->name);

    // start
    $this->post(route('test.start', $test->token), ['student_id' => $student->id])->assertRedirect();
    $attempt = TestAttempt::withoutGlobalScopes()->where('test_id', $test->id)->where('student_id', $student->id)->firstOrFail();

    // submit correct answer
    $this->post(route('test.submit', [$test->token, $attempt->id]), [
        'answers' => [$q->id => $right->id],
    ])->assertRedirect(route('test.result', [$test->token, $attempt->id]));

    $attempt->refresh();
    expect((float) $attempt->score)->toBe(2.0)
        ->and((float) $attempt->max_score)->toBe(2.0)
        ->and($attempt->isSubmitted())->toBeTrue();
});

it('enforces a single attempt per student', function () {
    [$client] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $student = Student::factory()->create(['client_id' => $client->id]);
    Enrollment::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'student_id' => $student->id]);
    $test = Test::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'is_published' => true]);
    Question::factory()->create(['client_id' => $client->id, 'test_id' => $test->id]);

    $this->post(route('test.start', $test->token), ['student_id' => $student->id]);
    $attempt = TestAttempt::withoutGlobalScopes()->firstOrFail();
    $this->post(route('test.submit', [$test->token, $attempt->id]), ['answers' => []]);

    // second start returns the same (submitted) attempt and redirects to result
    $this->post(route('test.start', $test->token), ['student_id' => $student->id])
        ->assertRedirect(route('test.result', [$test->token, $attempt->id]));

    expect(TestAttempt::withoutGlobalScopes()->where('test_id', $test->id)->count())->toBe(1);
});

it('blocks a non-enrolled student from starting', function () {
    [$client] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);
    $test = Test::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'is_published' => true]);
    $outsider = Student::factory()->create(['client_id' => $client->id]); // not enrolled

    $this->post(route('test.start', $test->token), ['student_id' => $outsider->id])
        ->assertSessionHasErrors('student_id');
});

it('does not expose a draft test publicly', function () {
    [$client] = tenantWithAdmin();
    $test = Test::factory()->draft()->create(['client_id' => $client->id]);

    $this->get(route('test.show', $test->token))->assertNotFound();
});
