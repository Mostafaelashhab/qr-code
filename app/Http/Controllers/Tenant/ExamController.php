<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreExamRequest;
use App\Models\Exam;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function store(StoreExamRequest $request, Group $group): RedirectResponse
    {
        $exam = $group->exams()->create($request->validated());

        return redirect()->route('tenant.exams.show', $exam)->with('status', __('messages.exam_saved'));
    }

    /**
     * Grade-entry roster for an exam.
     */
    public function show(Exam $exam): View
    {
        $exam->load('group');
        $students = $exam->group->students()->wherePivot('is_active', true)->orderBy('name')->get();
        $scores = $exam->grades()->pluck('score', 'student_id')->all();

        return view('tenant.exams.show', compact('exam', 'students', 'scores'));
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $group = $exam->group;
        $exam->delete();

        return redirect()->route('tenant.groups.show', $group)->with('status', __('messages.exam_deleted'));
    }
}
