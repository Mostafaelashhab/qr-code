<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;

class EnrollmentController extends Controller
{
    public function store(StoreEnrollmentRequest $request, Group $group): RedirectResponse
    {
        if (! $group->hasCapacity()) {
            return back()->withErrors(['student_id' => __('messages.group_full')]);
        }

        $group->enrollments()->create([
            'student_id' => $request->integer('student_id'),
            'enrolled_at' => now(),
        ]);

        return back()->with('status', __('messages.student_enrolled'));
    }

    public function destroy(Group $group, Enrollment $enrollment): RedirectResponse
    {
        abort_unless($enrollment->group_id === $group->id, 404);

        $enrollment->delete();

        return back()->with('status', __('messages.student_unenrolled'));
    }
}
