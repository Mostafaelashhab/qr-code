<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\View\View;

class GuardianPortalController extends Controller
{
    /**
     * Public, read-only parent portal for a single student, opened via an
     * unguessable token. No authentication; the token itself is the authorization,
     * so tenant scoping is bypassed.
     */
    public function show(string $token): View
    {
        $student = Student::withoutGlobalScopes()
            ->where('guardian_token', $token)
            ->with(['client', 'groups.subject'])
            ->firstOrFail();

        $attendances = Attendance::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->with('session.group')
            ->latest('id')
            ->take(20)
            ->get();

        $total = $attendances->count();
        $present = $attendances->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])->count();

        return view('portal.show', [
            'student' => $student,
            'client' => $student->client,
            'attendances' => $attendances,
            'attendanceRate' => $total > 0 ? (int) round($present / $total * 100) : null,
            'grades' => $student->grades()->withoutGlobalScopes()->with('exam.group')->latest('id')->take(20)->get(),
            'totalCharged' => $student->totalCharged(),
            'totalPaid' => $student->totalPaid(),
            'balance' => $student->balance(),
            'payments' => $student->payments()->withoutGlobalScopes()->with('group')->latest('paid_at')->take(10)->get(),
        ]);
    }
}
