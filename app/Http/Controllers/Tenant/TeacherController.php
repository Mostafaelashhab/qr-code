<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTeacherRequest;
use App\Http\Requests\Tenant\UpdateTeacherRequest;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::with('subject')->withCount('groups')->latest()->paginate(15);

        return view('tenant.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('tenant.teachers.create', [
            'teacher' => new Teacher,
            'subjects' => Subject::active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        Teacher::create($request->validated());

        return redirect()->route('tenant.teachers.index')->with('status', __('messages.teacher_saved'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('tenant.teachers.edit', [
            'teacher' => $teacher,
            'subjects' => Subject::active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->update($request->validated());

        return redirect()->route('tenant.teachers.index')->with('status', __('messages.teacher_saved'));
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('tenant.teachers.index')->with('status', __('messages.teacher_deleted'));
    }
}
