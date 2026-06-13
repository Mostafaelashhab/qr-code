<x-layouts.app :title="__('ui.payroll_report')">
    <form method="GET" class="mb-6 flex items-end gap-2">
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="mt-1 rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </form>

    <div class="mb-6 grid grid-cols-1 sm:max-w-xs">
        <x-stat-card :label="__('ui.total_earnings')" :value="number_format($totalEarnings, 2)" />
    </div>

    <x-card class="overflow-hidden !p-0">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-start">{{ __('ui.teacher') }}</th>
                    <th class="px-6 py-3 text-start">{{ __('ui.collected') }}</th>
                    <th class="px-6 py-3 text-end">{{ __('ui.earnings') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rows as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $row['name'] }}</td>
                        <td class="px-6 py-4 text-gray-600 tabular-nums">{{ number_format($row['collected'], 2) }}</td>
                        <td class="px-6 py-4 text-end font-semibold tabular-nums">{{ number_format($row['earnings'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</x-layouts.app>
