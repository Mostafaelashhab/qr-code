@php $totalSlots = collect($slotsByDay)->sum(fn ($slots) => $slots->count()); @endphp

<x-layouts.app :title="__('ui.timetable')">
    <x-page-header :title="__('ui.timetable')" :subtitle="$totalSlots.' '.__('ui.slots_this_week')" />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($weekdays as $day)
            @php $daySlots = $slotsByDay[$day->value] ?? collect(); @endphp
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3">
                    <h2 class="font-semibold tracking-tight text-gray-900">{{ $day->label() }}</h2>
                    @if ($daySlots->isNotEmpty())
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 tabular-nums">{{ $daySlots->count() }}</span>
                    @endif
                </div>
                <div class="p-4">
                    <ul class="space-y-2">
                        @forelse ($daySlots as $slot)
                            <li class="rounded-xl bg-gray-50 p-3 ring-1 ring-gray-100">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold tabular-nums text-indigo-700">{{ $slot->startLabel() }}–{{ $slot->endLabel() }}</span>
                                    @if ($slot->room)
                                        <span class="truncate text-xs text-gray-400">{{ $slot->room }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('tenant.groups.show', $slot->group_id) }}" class="mt-1.5 block truncate font-medium text-gray-900 hover:text-indigo-600">{{ $slot->group->name }}</a>
                                <span class="text-xs text-gray-500">{{ $slot->group->subject->name }}</span>
                            </li>
                        @empty
                            <li class="rounded-xl border border-dashed border-gray-200 px-3 py-6 text-center text-xs text-gray-400">{{ __('ui.no_slots') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
