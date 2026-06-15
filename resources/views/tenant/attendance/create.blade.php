@php
    use App\Enums\AttendanceStatus;
    $statuses = AttendanceStatus::cases();

    // Selected status takes a meaning-based colour (present=green, late=amber, absent=red, …).
    $checkedClasses = [
        'present' => 'peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:ring-emerald-600',
        'late'    => 'peer-checked:bg-amber-500 peer-checked:text-white peer-checked:ring-amber-500',
        'absent'  => 'peer-checked:bg-rose-600 peer-checked:text-white peer-checked:ring-rose-600',
        'excused' => 'peer-checked:bg-sky-600 peer-checked:text-white peer-checked:ring-sky-600',
    ];
    $defaultChecked = 'peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:ring-indigo-600';
    $initialOf = fn ($s): string => (string) \Illuminate\Support\Str::of($s->name)->trim()->substr(0, 1)->upper();
@endphp

<x-layouts.app :title="__('ui.take_attendance')">
    <x-page-header :title="__('ui.take_attendance')" :subtitle="$group->name.' · '.$group->subject->name" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.groups'), 'url' => route('tenant.groups.index')],
        ['label' => $group->name, 'url' => route('tenant.groups.show', $group)],
        ['label' => __('ui.take_attendance')],
    ]" />

    <form method="POST" action="{{ route('tenant.groups.attendance.store', $group) }}" class="space-y-6">
        @csrf
        <x-card>
            <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-form.field name="session_date" :label="__('ui.session_date')" type="date" :value="$date" required />
                <x-form.field name="note" :label="__('ui.notes')" />
            </div>

            @forelse ($students as $student)
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 py-3">
                    <div class="flex items-center gap-2.5">
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600">{{ $initialOf($student) }}</span>
                        <span class="text-sm font-medium text-gray-800">{{ $student->name }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($statuses as $status)
                            @php $value = $current[$student->id] ?? 'present'; @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="statuses[{{ $student->id }}]" value="{{ $status->value }}"
                                       class="peer sr-only" @checked($value === $status->value)>
                                <span class="inline-flex rounded-lg px-3 py-1 text-xs font-medium text-gray-500 ring-1 ring-gray-200 transition {{ $checkedClasses[$status->value] ?? $defaultChecked }}">
                                    {{ $status->label() }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm text-gray-500">{{ __('ui.no_enrolled_students') }}</p>
            @endforelse
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button variant="secondary" :href="route('tenant.groups.show', $group)">{{ __('ui.cancel') }}</x-button>
            @if ($students->isNotEmpty())
                <x-button type="submit">{{ __('ui.save_attendance') }}</x-button>
            @endif
        </div>
    </form>
</x-layouts.app>
