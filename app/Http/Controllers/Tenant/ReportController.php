<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('tenant.reports.index');
    }

    /**
     * Income vs. expenses for a month, with income broken down by group.
     */
    public function financial(Request $request): View
    {
        [$month, $start, $end] = $this->month($request);

        $payments = Payment::with('group')->whereBetween('paid_at', [$start, $end])->get();
        $income = (float) $payments->sum('amount');
        $expenses = (float) Expense::whereBetween('spent_at', [$start, $end])->sum('amount');

        $incomeByGroup = $payments
            ->groupBy(fn (Payment $payment): string => $payment->group?->name ?? __('ui.none'))
            ->map(fn ($group): float => (float) $group->sum('amount'))
            ->sortDesc();

        return view('tenant.reports.financial', [
            'month' => $month,
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
            'incomeByGroup' => $incomeByGroup,
        ]);
    }

    /**
     * Attendance rate per enrolled student for a group in a month.
     */
    public function attendance(Request $request): View
    {
        [$month, $start, $end] = $this->month($request);
        $groups = Group::orderBy('name')->get();
        $group = $this->selectedGroup($request, $groups);

        $rows = collect();
        $sessionCount = 0;

        if ($group) {
            $sessionIds = AttendanceSession::where('group_id', $group->id)
                ->whereBetween('session_date', [$start, $end])
                ->pluck('id');
            $sessionCount = $sessionIds->count();

            $presentByStudent = Attendance::whereIn('attendance_session_id', $sessionIds)
                ->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])
                ->get()
                ->groupBy('student_id')
                ->map->count();

            $rows = $group->students()->wherePivot('is_active', true)->orderBy('name')->get()
                ->map(function ($student) use ($presentByStudent, $sessionCount): array {
                    $present = (int) ($presentByStudent[$student->id] ?? 0);

                    return [
                        'name' => $student->name,
                        'present' => $present,
                        'sessions' => $sessionCount,
                        'rate' => $sessionCount > 0 ? round($present / $sessionCount * 100) : 0,
                    ];
                });
        }

        return view('tenant.reports.attendance', compact('month', 'groups', 'group', 'rows', 'sessionCount'));
    }

    /**
     * Which enrolled students have / have not paid for a group in a month.
     */
    public function collection(Request $request): View
    {
        [$month, $start, $end] = $this->month($request);
        $groups = Group::orderBy('name')->get();
        $group = $this->selectedGroup($request, $groups);

        $paid = collect();
        $unpaid = collect();

        if ($group) {
            $paidStudentIds = Payment::where('group_id', $group->id)
                ->where('for_month', $month)
                ->pluck('student_id')
                ->unique();

            $students = $group->students()->wherePivot('is_active', true)->orderBy('name')->get();
            [$paid, $unpaid] = $students->partition(fn ($student): bool => $paidStudentIds->contains($student->id));
        }

        return view('tenant.reports.collection', [
            'month' => $month,
            'groups' => $groups,
            'group' => $group,
            'paid' => $paid->values(),
            'unpaid' => $unpaid->values(),
            'expected' => $group ? (float) $group->monthly_fee * ($paid->count() + $unpaid->count()) : 0,
            'collected' => $group ? (float) $group->monthly_fee * $paid->count() : 0,
        ]);
    }

    /**
     * Teacher payroll: each teacher's share of their groups' collected revenue for a month.
     */
    public function payroll(Request $request): View
    {
        [$month, $start, $end] = $this->month($request);

        $rows = Teacher::with('groups')->orderBy('name')->get()->map(function (Teacher $teacher) use ($start, $end): array {
            $collected = 0.0;
            $earnings = 0.0;

            foreach ($teacher->groups as $group) {
                $groupCollected = (float) Payment::where('group_id', $group->id)
                    ->whereBetween('paid_at', [$start, $end])
                    ->sum('amount');

                $collected += $groupCollected;
                $earnings += $groupCollected * (float) $group->teacher_share / 100;
            }

            return [
                'name' => $teacher->name,
                'collected' => $collected,
                'earnings' => round($earnings, 2),
            ];
        });

        return view('tenant.reports.payroll', [
            'month' => $month,
            'rows' => $rows,
            'totalEarnings' => (float) $rows->sum('earnings'),
        ]);
    }

    /**
     * Resolve the requested month, returning [Y-m, start, end].
     *
     * @return array{0: string, 1: Carbon, 2: Carbon}
     */
    private function month(Request $request): array
    {
        $raw = $request->string('month')->toString();

        $date = preg_match('/^\d{4}-\d{2}$/', $raw)
            ? Carbon::createFromFormat('Y-m', $raw)->startOfMonth()
            : Carbon::now()->startOfMonth();

        return [$date->format('Y-m'), $date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    private function selectedGroup(Request $request, $groups): ?Group
    {
        $id = $request->integer('group_id');

        return $id ? $groups->firstWhere('id', $id) : $groups->first();
    }
}
