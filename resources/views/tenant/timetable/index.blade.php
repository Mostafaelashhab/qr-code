<x-layouts.app :title="__('ui.timetable')">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($weekdays as $day)
            @php $daySlots = $slotsByDay[$day->value] ?? collect(); @endphp
            <x-card :title="$day->label()">
                <ul class="space-y-2">
                    @forelse ($daySlots as $slot)
                        <li class="rounded-lg bg-gray-50 px-3 py-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">{{ $slot->startLabel() }}–{{ $slot->endLabel() }}</span>
                                <span class="text-xs text-gray-500">{{ $slot->room }}</span>
                            </div>
                            <a href="{{ route('tenant.groups.show', $slot->group_id) }}" class="text-indigo-600 hover:underline">
                                {{ $slot->group->name }}
                            </a>
                            <span class="text-xs text-gray-500">· {{ $slot->group->subject->name }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400">{{ __('ui.no_slots') }}</li>
                    @endforelse
                </ul>
            </x-card>
        @endforeach
    </div>
</x-layouts.app>
