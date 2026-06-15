<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Let the guardian opt in/out of WhatsApp reminders for this student, from
     * the same token-authorized portal.
     */
    public function toggleReminders(string $token): RedirectResponse
    {
        $student = Student::withoutGlobalScopes()
            ->where('guardian_token', $token)
            ->firstOrFail();

        $student->forceFill(['reminders_opt_out' => ! $student->reminders_opt_out])->save();

        return redirect()
            ->route('portal.show', $token)
            ->with('status', $student->reminders_opt_out
                ? __('portal.reminders_off_notice')
                : __('portal.reminders_on_notice'));
    }
}
