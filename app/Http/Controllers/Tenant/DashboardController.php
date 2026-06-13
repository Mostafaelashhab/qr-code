<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $client = $request->user()->client;

        $stats = [
            'students' => Student::count(),
            'groups' => Group::count(),
            'teachers' => Teacher::count(),
            'month_revenue' => Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
        ];

        $subscription = $client->activeSubscription()?->loadMissing('plan');
        $plan = $subscription?->plan;

        $usage = [
            'users' => ['used' => $client->users()->count(), 'limit' => $plan?->max_users],
            'students' => ['used' => $stats['students'], 'limit' => $plan?->max_students],
        ];

        $onboarding = [
            ['done' => Subject::exists(), 'label' => __('ui.onboard_subject'), 'url' => route('tenant.subjects.create')],
            ['done' => Teacher::exists(), 'label' => __('ui.onboard_teacher'), 'url' => route('tenant.teachers.create')],
            ['done' => Group::exists(), 'label' => __('ui.onboard_group'), 'url' => route('tenant.groups.create')],
            ['done' => $stats['students'] > 0, 'label' => __('ui.onboard_student'), 'url' => route('tenant.students.create')],
        ];

        return view('tenant.dashboard', [
            'client' => $client,
            'stats' => $stats,
            'subscription' => $subscription,
            'usage' => $usage,
            'onboarding' => $onboarding,
            'onboardingDone' => collect($onboarding)->every(fn (array $step): bool => $step['done']),
            'recentPayments' => Payment::with(['student', 'group'])->latest('paid_at')->latest()->take(5)->get(),
            'revenueByMonth' => $this->monthlySeries(fn (Carbon $from, Carbon $to) => (float) Payment::whereBetween('paid_at', [$from, $to])->sum('amount')),
            'studentsByMonth' => $this->monthlySeries(fn (Carbon $from, Carbon $to) => Student::whereBetween('created_at', [$from, $to])->count()),
            'attendanceRate' => $this->attendanceRate(),
        ]);
    }

    /**
     * Build a label => value series for the last 6 months.
     *
     * @param  callable(Carbon, Carbon): (int|float)  $resolver
     * @return array<string, int|float>
     */
    private function monthlySeries(callable $resolver): array
    {
        $series = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $series[$month->isoFormat('MMM')] = $resolver($month->copy()->startOfMonth(), $month->copy()->endOfMonth());
        }

        return $series;
    }

    /**
     * Overall present/late attendance percentage.
     */
    private function attendanceRate(): int
    {
        $total = Attendance::count();

        if ($total === 0) {
            return 0;
        }

        $present = Attendance::whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])->count();

        return (int) round($present / $total * 100);
    }
}
