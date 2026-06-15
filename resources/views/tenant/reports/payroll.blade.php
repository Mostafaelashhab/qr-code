@php $currency = auth()->user()->client?->currency; @endphp

<x-layouts.app :title="__('ui.payroll_report')">
    <x-page-header :title="__('ui.payroll_report')" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.reports'), 'url' => route('tenant.reports.index')],
        ['label' => __('ui.payroll_report')],
    ]" />

    <x-filter-bar>
        <div>
            <label for="month" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </x-filter-bar>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:max-w-xs">
        <x-stat-card icon="cash" color="emerald" :label="__('ui.total_earnings')" :value="number_format($totalEarnings, 2)" :hint="$currency" />
    </div>

    <x-data-table>
        <x-slot:head>
            <th class="px-6 py-3.5 text-start">{{ __('ui.teacher') }}</th>
            <th class="px-6 py-3.5 text-start">{{ __('ui.collected') }}</th>
            <th class="px-6 py-3.5 text-end">{{ __('ui.earnings') }}</th>
        </x-slot:head>

        @forelse ($rows as $row)
            <tr class="transition hover:bg-gray-50/70">
                <td class="px-6 py-3.5 font-medium text-gray-900">{{ $row['name'] }}</td>
                <td class="px-6 py-3.5 text-gray-600 tabular-nums">{{ number_format($row['collected'], 2) }}</td>
                <td class="px-6 py-3.5 text-end font-semibold tabular-nums text-gray-900">{{ number_format($row['earnings'], 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</td></tr>
        @endforelse
    </x-data-table>
</x-layouts.app>
