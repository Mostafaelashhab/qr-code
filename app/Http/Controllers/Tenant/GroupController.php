<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreGroupRequest;
use App\Http\Requests\Tenant\UpdateGroupRequest;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = Group::with(['subject', 'teacher'])
            ->withCount(['enrollments' => fn ($query) => $query->where('is_active', true)])
            ->latest()
            ->paginate(15);

        return view('tenant.groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('tenant.groups.create', [
            'group' => new Group,
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $group = Group::create($request->validated());

        return redirect()->route('tenant.groups.show', $group)->with('status', __('messages.group_saved'));
    }

    public function show(Group $group): View
    {
        $group->load(['subject', 'teacher', 'students', 'attendanceSessions', 'exams', 'timetableSlots']);

        // Students of this tenant not yet enrolled, for the enrollment picker.
        $availableStudents = Student::active()
            ->whereDoesntHave('enrollments', fn ($query) => $query->where('group_id', $group->id))
            ->orderBy('name')
            ->get();

        return view('tenant.groups.show', compact('group', 'availableStudents'));
    }

    public function edit(Group $group): View
    {
        return view('tenant.groups.edit', [
            'group' => $group,
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdateGroupRequest $request, Group $group): RedirectResponse
    {
        $group->update($request->validated());

        return redirect()->route('tenant.groups.show', $group)->with('status', __('messages.group_saved'));
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return redirect()->route('tenant.groups.index')->with('status', __('messages.group_deleted'));
    }

    /**
     * @return array{subjects: Collection<int, Subject>, teachers: Collection<int, Teacher>}
     */
    private function formOptions(): array
    {
        return [
            'subjects' => Subject::active()->orderBy('name')->get(),
            'teachers' => Teacher::active()->orderBy('name')->get(),
        ];
    }
}
