<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreAttendanceRequest;
use App\Models\AttendanceSession;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Roster form to take (or amend) attendance for a given date.
     */
    public function create(Request $request, Group $group): View
    {
        $date = $request->date('date')?->format('Y-m-d') ?? now()->format('Y-m-d');

        $students = $group->students()->wherePivot('is_active', true)->orderBy('name')->get();

        $existing = AttendanceSession::where('group_id', $group->id)
            ->where('session_date', $date)
            ->with('attendances')
            ->first();

        $current = $existing
            ? $existing->attendances->pluck('status.value', 'student_id')->all()
            : [];

        return view('tenant.attendance.create', compact('group', 'students', 'date', 'current'));
    }

    public function store(StoreAttendanceRequest $request, Group $group): RedirectResponse
    {
        $enrolledIds = $group->students()->pluck('students.id')->all();

        DB::transaction(function () use ($request, $group, $enrolledIds): void {
            $session = $group->attendanceSessions()->updateOrCreate(
                ['session_date' => $request->date('session_date')->format('Y-m-d')],
                ['note' => $request->input('note')],
            );

            foreach ($request->validated('statuses') as $studentId => $status) {
                if (! in_array((int) $studentId, $enrolledIds, true)) {
                    continue;
                }

                $session->attendances()->updateOrCreate(
                    ['student_id' => $studentId],
                    ['status' => $status],
                );
            }
        });

        return redirect()
            ->route('tenant.groups.show', $group)
            ->with('status', __('messages.attendance_saved'));
    }

    public function show(AttendanceSession $session): View
    {
        $session->load(['group.subject', 'attendances.student']);

        return view('tenant.attendance.show', compact('session'));
    }

    /**
     * Camera-based QR check-in page for a group.
     */
    public function scan(Group $group): View
    {
        $group->loadCount(['enrollments' => fn ($query) => $query->where('is_active', true)]);

        return view('tenant.attendance.scan', compact('group'));
    }

    /**
     * Mark a scanned student present in today's session. Returns JSON for the scanner UI.
     */
    public function scanStore(Request $request, Group $group): JsonResponse
    {
        $token = $request->validate(['token' => ['required', 'string']])['token'];

        $student = Student::where('qr_token', $token)->first();

        if ($student === null) {
            return response()->json(['ok' => false, 'message' => __('attendance.qr_not_found')], 404);
        }

        $enrolled = $group->students()
            ->wherePivot('is_active', true)
            ->where('students.id', $student->id)
            ->exists();

        if (! $enrolled) {
            return response()->json(['ok' => false, 'message' => __('attendance.qr_not_enrolled', ['name' => $student->name])], 422);
        }

        $session = $group->attendanceSessions()->firstOrCreate(['session_date' => now()->toDateString()]);
        $session->attendances()->updateOrCreate(
            ['student_id' => $student->id],
            ['status' => AttendanceStatus::Present],
        );

        return response()->json([
            'ok' => true,
            'name' => $student->name,
            'message' => __('attendance.qr_checked_in', ['name' => $student->name]),
        ]);
    }
}

