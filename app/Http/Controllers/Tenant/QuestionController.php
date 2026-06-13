<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\QuestionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreQuestionRequest;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;

class QuestionController extends Controller
{
    public function store(StoreQuestionRequest $request, Test $test): RedirectResponse
    {
        $type = $request->enum('type', QuestionType::class);

        $question = $test->questions()->create([
            'body' => $request->string('body')->toString(),
            'type' => $type,
            'points' => $request->integer('points'),
            'sort_order' => (int) $test->questions()->max('sort_order') + 1,
        ]);

        if ($type === QuestionType::Mcq) {
            $correct = (int) $request->input('correct');
            $sort = 0;

            foreach ((array) $request->input('options', []) as $index => $body) {
                if (blank($body)) {
                    continue;
                }

                $question->options()->create([
                    'body' => $body,
                    'is_correct' => $index === $correct,
                    'sort_order' => $sort++,
                ]);
            }
        } else {
            $correctTrue = $request->input('correct') === 'true';

            $question->options()->create(['body' => __('tests.true'), 'is_correct' => $correctTrue, 'sort_order' => 0]);
            $question->options()->create(['body' => __('tests.false'), 'is_correct' => ! $correctTrue, 'sort_order' => 1]);
        }

        return back()->with('status', __('messages.question_added'));
    }

    public function destroy(Test $test, Question $question): RedirectResponse
    {
        abort_unless($question->test_id === $test->id, 404);

        $question->delete();

        return back()->with('status', __('messages.question_deleted'));
    }
}
