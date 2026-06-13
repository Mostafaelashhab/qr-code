<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSubjectRequest;
use App\Http\Requests\Tenant\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::withCount(['teachers', 'groups'])->latest()->paginate(15);

        return view('tenant.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('tenant.subjects.create', ['subject' => new Subject]);
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()->route('tenant.subjects.index')->with('status', __('messages.subject_saved'));
    }

    public function edit(Subject $subject): View
    {
        return view('tenant.subjects.edit', compact('subject'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()->route('tenant.subjects.index')->with('status', __('messages.subject_saved'));
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->groups()->exists()) {
            return back()->withErrors(['delete' => __('messages.subject_has_groups')]);
        }

        $subject->delete();

        return redirect()->route('tenant.subjects.index')->with('status', __('messages.subject_deleted'));
    }
}
