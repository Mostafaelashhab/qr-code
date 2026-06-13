<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreGradeRequest;
use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function store(StoreGradeRequest $request, Exam $exam): RedirectResponse
    {
        $enrolledIds = $exam->group->students()->pluck('students.id')->all();

        DB::transaction(function () use ($request, $exam, $enrolledIds): void {
            foreach ($request->validated('scores') as $studentId => $score) {
                if ($score === null || ! in_array((int) $studentId, $enrolledIds, true)) {
                    continue;
                }

                $exam->grades()->updateOrCreate(
                    ['student_id' => $studentId],
                    ['score' => min((float) $score, (float) $exam->max_score)],
                );
            }
        });

        return redirect()->route('tenant.exams.show', $exam)->with('status', __('messages.grades_saved'));
    }
}
