<x-layouts.app :title="__('ui.financial_report')">
    <form method="GET" class="mb-6 flex items-end gap-2">
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="mt-1 rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </form>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card :label="__('ui.total_income')" :value="number_format($income, 2)" />
        <x-stat-card :label="__('ui.total_expenses')" :value="number_format($expenses, 2)" />
        <x-stat-card :label="__('ui.net_profit')" :value="number_format($net, 2)" />
    </div>

    <div class="mt-6">
        <x-card :title="__('ui.income_by_group')">
            <ul class="divide-y divide-gray-100">
                @forelse ($incomeByGroup as $name => $amount)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="font-medium">{{ $name }}</span>
                        <span class="text-gray-600">{{ number_format($amount, 2) }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
