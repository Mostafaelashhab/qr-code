@php
    $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all();
@endphp

<x-layouts.app :title="__('ui.attendance_report')">
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-2">
        <div>
            <label for="group_id" class="block text-sm font-medium text-gray-700">{{ __('ui.group') }}</label>
            <select name="group_id" id="group_id"
                    class="mt-1 rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                @foreach ($groupOptions as $id => $name)
                    <option value="{{ $id }}" @selected($group?->id === $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="mt-1 rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </form>

    @if (! $group)
        <x-card><p class="text-sm text-gray-500">{{ __('ui.select_group') }}</p></x-card>
    @else
        <x-card class="overflow-hidden !p-0">
            <div class="border-b border-gray-100 px-6 py-3 text-sm text-gray-500">
                {{ $group->name }} · {{ $sessionCount }} {{ __('ui.sessions') }}
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.student') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.present_count') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.attendance_rate') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $row)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $row['name'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $row['present'] }} / {{ $row['sessions'] }}</td>
                            <td class="px-6 py-4 text-end">
                                <x-badge :color="$row['rate'] >= 75 ? 'emerald' : ($row['rate'] >= 50 ? 'amber' : 'rose')">
                                    {{ $row['rate'] }}%
                                </x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_enrolled_students') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    @endif
</x-layouts.app>
