@php
    $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all();
@endphp

<x-layouts.app :title="__('ui.attendance_report')">
    <x-page-header :title="__('ui.attendance_report')" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.reports'), 'url' => route('tenant.reports.index')],
        ['label' => __('ui.attendance_report')],
    ]" />

    <x-filter-bar>
        <div>
            <label for="group_id" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.group') }}</label>
            <select name="group_id" id="group_id"
                    class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
                @foreach ($groupOptions as $id => $name)
                    <option value="{{ $id }}" @selected($group?->id === $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="month" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </x-filter-bar>

    @if (! $group)
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.select_group')" />
        </x-card>
    @else
        <x-data-table>
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.student') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.present_count') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.attendance_rate') }}</th>
            </x-slot:head>

            @forelse ($rows as $row)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $row['name'] }}</td>
                    <td class="px-6 py-3.5 text-gray-600 tabular-nums">{{ $row['present'] }} / {{ $row['sessions'] }}</td>
                    <td class="px-6 py-3.5 text-end">
                        <x-badge :color="$row['rate'] >= 75 ? 'emerald' : ($row['rate'] >= 50 ? 'amber' : 'rose')">{{ $row['rate'] }}%</x-badge>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">{{ __('ui.no_enrolled_students') }}</td></tr>
            @endforelse

            <x-slot:footer>
                <p class="text-sm text-gray-500">{{ $group->name }} · {{ $sessionCount }} {{ __('ui.sessions') }}</p>
            </x-slot:footer>
        </x-data-table>
    @endif
</x-layouts.app>
