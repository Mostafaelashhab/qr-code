@php
    $currency = auth()->user()->client?->currency;
    $initialOf = fn ($p): string => (string) \Illuminate\Support\Str::of($p->student->name)->trim()->substr(0, 1)->upper();
@endphp

<x-layouts.app :title="__('ui.payments')">
    <x-page-header :title="__('ui.payments')" :subtitle="number_format($payments->total()).' '.__('ui.payments')">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('tenant.exports.payments')">{{ __('ui.export_csv') }}</x-button>
            <x-button :href="route('tenant.payments.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_payment') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Summary --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-stat-card icon="cash" color="emerald" :label="__('ui.this_month_revenue')"
                     :value="number_format((float) $monthTotal, 2)" :hint="$currency" />
        <x-stat-card icon="chart" color="indigo" :label="__('ui.payments')" :value="number_format($payments->total())" />
    </div>

    @if ($payments->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_payment')" :action-href="route('tenant.payments.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.student') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.group') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.amount') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.method') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.paid_at') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($payments as $payment)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($payment) }}</span>
                            <span class="font-medium text-gray-900">{{ $payment->student->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $payment->group?->name ?? '—' }}</td>
                    <td class="px-6 py-3.5 font-semibold tabular-nums text-gray-900">{{ number_format((float) $payment->amount, 2) }}</td>
                    <td class="px-6 py-3.5"><x-badge color="gray">{{ $payment->method->label() }}</x-badge></td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $payment->paid_at->isoFormat('LL') }}</td>
                    <td class="px-6 py-3.5">
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
            @endforeach

            <x-slot:footer>{{ $payments->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($payments as $payment)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($payment) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $payment->student->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $payment->group?->name ?? '—' }} · {{ $payment->paid_at->isoFormat('LL') }}</p>
                        </div>
                        <span class="shrink-0 font-semibold tabular-nums text-gray-900">{{ number_format((float) $payment->amount, 2) }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                        <x-badge color="gray">{{ $payment->method->label() }}</x-badge>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('tenant.payments.receipt', $payment) }}" class="font-medium text-indigo-600">{{ __('ui.print') }}</a>
                            <form method="POST" action="{{ route('tenant.payments.destroy', $payment) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-medium text-rose-600">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $payments->links() }}</div>
    @endif
</x-layouts.app>
