@php
    use App\Enums\Feature;
    $studentOptions = $availableStudents->mapWithKeys(fn ($s) => [$s->id => $s->name])->all();
    $client = auth()->user()->client;
@endphp

<x-layouts.app :title="$group->name">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ $group->name }}</h2>
            <p class="text-sm text-gray-500">{{ $group->subject->name }} · {{ $group->teacher?->name ?? '—' }} · {{ $group->schedule ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            <x-button variant="secondary" :href="route('tenant.groups.edit', $group)">{{ __('ui.edit') }}</x-button>
            @if ($client?->hasFeature(Feature::Attendance))
                <x-button variant="secondary" :href="route('tenant.groups.attendance.scan', $group)">{{ __('ui.qr_checkin') }}</x-button>
                <x-button :href="route('tenant.groups.attendance.create', $group)">{{ __('ui.take_attendance') }}</x-button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card :title="__('ui.enrolled_students')">
            <form method="POST" action="{{ route('tenant.groups.students.store', $group) }}" class="mb-4 flex items-end gap-2">
                @csrf
                <div class="flex-1">
                    <x-form.select name="student_id" :label="__('ui.enroll_student')" :options="$studentOptions" :placeholder="'—'" required />
                </div>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </form>

            <ul class="divide-y divide-gray-100">
                @forelse ($group->enrollments->where('is_active', true) as $enrollment)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <a href="{{ route('tenant.students.show', $enrollment->student_id) }}" class="font-medium text-indigo-600 hover:underline">
                            {{ $group->students->firstWhere('id', $enrollment->student_id)?->name }}
                        </a>
                        <form method="POST" action="{{ route('tenant.groups.students.destroy', [$group, $enrollment]) }}"
                              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.remove') }}</button>
                        </form>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_enrolled_students') }}</li>
                @endforelse
            </ul>
        </x-card>

        @if ($client?->hasFeature(Feature::Attendance))
        <x-card :title="__('ui.sessions')">
            <ul class="divide-y divide-gray-100">
                @forelse ($group->attendanceSessions->sortByDesc('session_date') as $session)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <a href="{{ route('tenant.attendance.show', $session) }}" class="font-medium text-indigo-600 hover:underline">
                            {{ $session->session_date->isoFormat('LL') }}
                        </a>
                        <span class="text-xs text-gray-500">{{ $session->note }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
        @endif
    </div>

    @if ($client?->hasFeature(Feature::Timetable))
    <div class="mt-6">
        <x-card :title="__('ui.timetable')">
            @php $weekdayOptions = collect(\App\Enums\Weekday::cases())->mapWithKeys(fn ($d) => [$d->value => $d->label()])->all(); @endphp
            <form method="POST" action="{{ route('tenant.timetable.store', $group) }}" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-5 sm:items-end">
                @csrf
                <x-form.select name="weekday" :label="__('ui.weekday')" :options="$weekdayOptions" required />
                <x-form.field name="start_time" :label="__('ui.start_time')" type="time" value="16:00" required />
                <x-form.field name="end_time" :label="__('ui.end_time')" type="time" value="17:30" required />
                <x-form.field name="room" :label="__('ui.room')" />
                <x-button type="submit">{{ __('ui.add_slot') }}</x-button>
            </form>

            <ul class="divide-y divide-gray-100">
                @forelse ($group->timetableSlots->sortBy('weekday') as $slot)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="font-medium">{{ $slot->weekday->label() }} · {{ $slot->startLabel() }}–{{ $slot->endLabel() }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500">{{ $slot->room }}</span>
                            <form method="POST" action="{{ route('tenant.timetable.destroy', $slot) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.remove') }}</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_slots') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
    @endif

    @if ($client?->hasFeature(Feature::Exams))
    <div class="mt-6">
        <x-card :title="__('ui.exams')">
            <form method="POST" action="{{ route('tenant.exams.store', $group) }}" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-4 sm:items-end">
                @csrf
                <x-form.field name="name" :label="__('ui.name')" required />
                <x-form.field name="exam_date" :label="__('ui.exam_date')" type="date" :value="now()->toDateString()" required />
                <x-form.field name="max_score" :label="__('ui.max_score')" type="number" min="1" value="100" required />
                <x-button type="submit">{{ __('ui.new_exam') }}</x-button>
            </form>

            <ul class="divide-y divide-gray-100">
                @forelse ($group->exams->sortByDesc('exam_date') as $exam)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <a href="{{ route('tenant.exams.show', $exam) }}" class="font-medium text-indigo-600 hover:underline">{{ $exam->name }}</a>
                        <span class="text-xs text-gray-500">{{ $exam->exam_date->isoFormat('LL') }} · {{ __('ui.max_score') }} {{ (int) $exam->max_score }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
    @endif

    <div class="mt-6">
        <x-button variant="secondary" :href="route('tenant.groups.index')">{{ __('ui.back') }}</x-button>
    </div>
</x-layouts.app>
