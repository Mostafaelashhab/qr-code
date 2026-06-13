<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestAttemptController extends Controller
{
    /**
     * Test intro page: pick the student, then start.
     */
    public function show(string $token): View
    {
        $test = $this->findTest($token);
        $students = $test->group->students()->wherePivot('is_active', true)->orderBy('name')->get();

        return view('tests.show', compact('test', 'students'));
    }

    /**
     * Begin (or resume) a single attempt for the chosen student.
     */
    public function start(Request $request, string $token): RedirectResponse
    {
        $test = $this->findTest($token);

        if (! $test->isAvailable()) {
            return back()->withErrors(['student_id' => __('tests.not_available')]);
        }

        $request->validate(['student_id' => ['required', 'integer']]);
        $enrolledIds = $test->group->students()->wherePivot('is_active', true)->pluck('students.id');

        if (! $enrolledIds->contains($request->integer('student_id'))) {
            return back()->withErrors(['student_id' => __('tests.not_enrolled')]);
        }

        $attempt = TestAttempt::withoutGlobalScopes()->firstOrCreate(
            ['test_id' => $test->id, 'student_id' => $request->integer('student_id')],
            ['client_id' => $test->client_id, 'started_at' => now()],
        );

        if ($attempt->isSubmitted()) {
            return redirect()->route('test.result', [$token, $attempt->id]);
        }

        return redirect()->route('test.take', [$token, $attempt->id]);
    }

    public function take(string $token, int $attempt): View|RedirectResponse
    {
        $test = $this->findTest($token);
        $attempt = $this->findAttempt($test, $attempt);

        if ($attempt->isSubmitted()) {
            return redirect()->route('test.result', [$token, $attempt->id]);
        }

        $questions = $test->questions()->with('options')->get();

        if ($test->shuffle) {
            $questions = $questions->shuffle()->map(function (Question $question): Question {
                $question->setRelation('options', $question->options->shuffle());

                return $question;
            });
        }

        $deadline = $attempt->started_at->copy()->addMinutes($test->duration_minutes);
        $secondsLeft = max(0, (int) now()->diffInSeconds($deadline, false));

        return view('tests.take', compact('test', 'attempt', 'questions', 'secondsLeft'));
    }

    public function submit(Request $request, string $token, int $attempt): RedirectResponse
    {
        $test = $this->findTest($token);
        $attempt = $this->findAttempt($test, $attempt);

        if ($attempt->isSubmitted()) {
            return redirect()->route('test.result', [$token, $attempt->id]);
        }

        $answers = (array) $request->input('answers', []);

        DB::transaction(function () use ($test, $attempt, $answers): void {
            $score = 0;
            $maxScore = 0;

            foreach ($test->questions()->with('options')->get() as $question) {
                $maxScore += $question->points;

                $selectedId = $answers[$question->id] ?? null;
                $option = $selectedId ? $question->options->firstWhere('id', (int) $selectedId) : null;
                $isCorrect = (bool) $option?->is_correct;

                if ($isCorrect) {
                    $score += $question->points;
                }

                $attempt->answers()->create([
                    'client_id' => $test->client_id,
                    'question_id' => $question->id,
                    'question_option_id' => $option?->id,
                    'is_correct' => $isCorrect,
                ]);
            }

            $attempt->update([
                'score' => $score,
                'max_score' => $maxScore,
                'submitted_at' => now(),
            ]);
        });

        return redirect()->route('test.result', [$token, $attempt->id]);
    }

    public function result(string $token, int $attempt): View
    {
        $test = $this->findTest($token);
        $attempt = $this->findAttempt($test, $attempt)->load('student');

        return view('tests.result', compact('test', 'attempt'));
    }

    private function findTest(string $token): Test
    {
        return Test::withoutGlobalScopes()
            ->published()
            ->with('group')
            ->where('token', $token)
            ->firstOrFail();
    }

    private function findAttempt(Test $test, int $attemptId): TestAttempt
    {
        return TestAttempt::withoutGlobalScopes()
            ->where('test_id', $test->id)
            ->findOrFail($attemptId);
    }
}
