<x-layouts.app :title="__('ui.payments')">
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2 flex items-center justify-end gap-2">
            <x-button variant="secondary" :href="route('tenant.exports.payments')">{{ __('ui.export_csv') }}</x-button>
            <x-button :href="route('tenant.payments.create')">{{ __('ui.new_payment') }}</x-button>
        </div>
        <x-stat-card :label="__('ui.this_month_revenue')" :value="number_format((float) $monthTotal, 2)" />
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.student') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.group') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.amount') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.method') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.paid_at') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $payment->student->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $payment->group?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $payment->method->label() }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $payment->paid_at->isoFormat('LL') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('tenant.payments.receipt', $payment) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.print') }}</a>
                                    <form method="POST" action="{{ route('tenant.payments.destroy', $payment) }}"
                                          onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state :action-label="__('ui.new_payment')" :action-href="route('tenant.payments.create')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $payments->links() }}</div>
</x-layouts.app>
