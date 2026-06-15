@php $currency = auth()->user()->client?->currency; @endphp

<x-layouts.app :title="__('ui.financial_report')">
    <x-page-header :title="__('ui.financial_report')" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.reports'), 'url' => route('tenant.reports.index')],
        ['label' => __('ui.financial_report')],
    ]" />

    <x-filter-bar>
        <div>
            <label for="month" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </x-filter-bar>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card icon="cash" color="emerald" :label="__('ui.total_income')" :value="number_format($income, 2)" :hint="$currency" />
        <x-stat-card icon="cash" color="amber" :label="__('ui.total_expenses')" :value="number_format($expenses, 2)" :hint="$currency" />
        <x-stat-card icon="chart" :color="$net >= 0 ? 'emerald' : 'rose'" :label="__('ui.net_profit')" :value="number_format($net, 2)" :hint="$currency" />
    </div>

    <div class="mt-6">
        <x-card :title="__('ui.income_by_group')">
            <ul class="divide-y divide-gray-100">
                @forelse ($incomeByGroup as $name => $amount)
                    <li class="flex items-center justify-between gap-4 py-3 text-sm">
                        <span class="font-medium text-gray-700">{{ $name }}</span>
                        <span class="tabular-nums font-semibold text-gray-900">{{ number_format($amount, 2) }}</span>
                    </li>
                @empty
                    <li class="py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
