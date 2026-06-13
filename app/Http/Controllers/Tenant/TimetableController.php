<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Weekday;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTimetableSlotRequest;
use App\Models\Group;
use App\Models\TimetableSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TimetableController extends Controller
{
    /**
     * Weekly grid of all slots grouped by weekday.
     */
    public function index(): View
    {
        $slots = TimetableSlot::with('group.subject')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (TimetableSlot $slot): int => $slot->weekday->value);

        return view('tenant.timetable.index', [
            'slotsByDay' => $slots,
            'weekdays' => Weekday::ordered(),
        ]);
    }

    public function store(StoreTimetableSlotRequest $request, Group $group): RedirectResponse
    {
        if ($this->teacherHasConflict($group, $request)) {
            return back()->withInput()->withErrors(['start_time' => __('messages.timetable_conflict')]);
        }

        $group->timetableSlots()->create($request->validated());

        return back()->with('status', __('messages.slot_saved'));
    }

    public function destroy(TimetableSlot $slot): RedirectResponse
    {
        $slot->delete();

        return back()->with('status', __('messages.slot_deleted'));
    }

    /**
     * Whether the group's teacher is already booked on an overlapping slot that weekday.
     */
    private function teacherHasConflict(Group $group, StoreTimetableSlotRequest $request): bool
    {
        if ($group->teacher_id === null) {
            return false;
        }

        return TimetableSlot::query()
            ->where('weekday', $request->integer('weekday'))
            ->whereHas('group', fn ($query) => $query->where('teacher_id', $group->teacher_id))
            ->where('start_time', '<', $request->string('end_time')->toString())
            ->where('end_time', '>', $request->string('start_time')->toString())
            ->exists();
    }
}
