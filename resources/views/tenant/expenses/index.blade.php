@php $currency = auth()->user()->client?->currency; @endphp

<x-layouts.app :title="__('ui.expenses')">
    <x-page-header :title="__('ui.expenses')" :subtitle="number_format($expenses->total()).' '.__('ui.expenses')">
        <x-slot:actions>
            <x-button :href="route('tenant.expenses.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_expense') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Summary --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-stat-card icon="cash" color="amber" :label="__('ui.this_month_expenses')"
                     :value="number_format((float) $monthTotal, 2)" :hint="$currency" />
        <x-stat-card icon="chart" color="indigo" :label="__('ui.expenses')" :value="number_format($expenses->total())" />
    </div>

    @if ($expenses->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_expense')" :action-href="route('tenant.expenses.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.title') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.category') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.amount') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.spent_at') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($expenses as $expense)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $expense->title }}</td>
                    <td class="px-6 py-3.5"><x-badge color="gray">{{ $expense->category->label() }}</x-badge></td>
                    <td class="px-6 py-3.5 font-semibold tabular-nums text-gray-900">{{ number_format((float) $expense->amount, 2) }}</td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $expense->spent_at->isoFormat('LL') }}</td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('tenant.expenses.edit', $expense) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                            <form method="POST" action="{{ route('tenant.expenses.destroy', $expense) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $expenses->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($expenses as $expense)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $expense->title }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $expense->spent_at->isoFormat('LL') }}</p>
                        </div>
                        <span class="shrink-0 font-semibold tabular-nums text-gray-900">{{ number_format((float) $expense->amount, 2) }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                        <x-badge color="gray">{{ $expense->category->label() }}</x-badge>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('tenant.expenses.edit', $expense) }}" class="font-medium text-indigo-600">{{ __('ui.edit') }}</a>
                            <form method="POST" action="{{ route('tenant.expenses.destroy', $expense) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-medium text-rose-600">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $expenses->links() }}</div>
    @endif
</x-layouts.app>
