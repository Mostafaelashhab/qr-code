<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\Reminders\SendAbsenceReminders;
use App\Actions\Reminders\SendPaymentReminders;
use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function absence(AttendanceSession $session, SendAbsenceReminders $action): RedirectResponse
    {
        $count = $action->execute($session);

        return back()->with('status', __('messages.reminders_sent', ['count' => $count]));
    }

    public function payment(Request $request, SendPaymentReminders $action): RedirectResponse
    {
        $request->validate([
            'group_id' => ['required', 'integer'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $group = Group::findOrFail($request->integer('group_id'));
        $count = $action->execute($group, $request->string('month')->toString());

        return back()->with('status', __('messages.reminders_sent', ['count' => $count]));
    }
}
