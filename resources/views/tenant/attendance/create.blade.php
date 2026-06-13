@php
    use App\Enums\AttendanceStatus;
    $statuses = AttendanceStatus::cases();
@endphp

<x-layouts.app :title="__('ui.take_attendance')">
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ $group->name }}</h2>
        <p class="text-sm text-gray-500">{{ $group->subject->name }}</p>
    </div>

    <form method="POST" action="{{ route('tenant.groups.attendance.store', $group) }}" class="space-y-6">
        @csrf
        <x-card>
            <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-form.field name="session_date" :label="__('ui.session_date')" type="date" :value="$date" required />
                <x-form.field name="note" :label="__('ui.notes')" />
            </div>

            @forelse ($students as $student)
                <div class="flex items-center justify-between gap-4 border-t border-gray-100 py-3">
                    <span class="text-sm font-medium">{{ $student->name }}</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($statuses as $status)
                            @php $value = $current[$student->id] ?? 'present'; @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="statuses[{{ $student->id }}]" value="{{ $status->value }}"
                                       class="peer sr-only" @checked($value === $status->value)>
                                <span class="inline-flex rounded-lg px-3 py-1 text-xs font-medium ring-1 ring-gray-200 text-gray-500
                                    peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:ring-indigo-600">
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
