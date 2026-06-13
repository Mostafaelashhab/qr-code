<x-layouts.app :title="__('ui.expenses')">
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2 flex items-center justify-end">
            <x-button :href="route('tenant.expenses.create')">{{ __('ui.new_expense') }}</x-button>
        </div>
        <x-stat-card :label="__('ui.this_month_expenses')" :value="number_format((float) $monthTotal, 2)" />
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.title') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.category') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.amount') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.spent_at') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->title }}</td>
                            <td class="px-6 py-4"><x-badge>{{ $expense->category->label() }}</x-badge></td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format((float) $expense->amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $expense->spent_at->isoFormat('LL') }}</td>
                            <td class="px-6 py-4">
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
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state :action-label="__('ui.new_expense')" :action-href="route('tenant.expenses.create')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $expenses->links() }}</div>
</x-layouts.app>
