@php use App\Enums\PaymentRequestStatus; @endphp

<x-layouts.app :title="__('ui.subscription_payments')">
    <x-page-header :title="__('ui.subscription_payments')" :subtitle="number_format($payments->total()).' '.__('ui.subscription_payments')" />

    <x-filter-bar>
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.status') }}</label>
            <select name="status" id="status" onchange="this.form.submit()"
                    class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
                <option value="">{{ __('ui.all') }}</option>
                @foreach (PaymentRequestStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </x-filter-bar>

    @if ($payments->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.no_results')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.center') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.plan') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.amount') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.payment_channel') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.transfer_reference') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.status') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.review') }}</th>
            </x-slot:head>

            @foreach ($payments as $payment)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $payment->client->name }}</td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $payment->plan->name }}</td>
                    <td class="px-6 py-3.5 font-semibold tabular-nums text-gray-900">{{ number_format((float) $payment->amount, 2) }}</td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $payment->channel->label() }}</td>
                    <td class="px-6 py-3.5">
                        <span class="font-mono text-xs text-gray-700" dir="ltr">{{ $payment->reference }}</span>
                        @if ($payment->receipt_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank" class="ms-2 text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.receipt') }}</a>
                        @endif
                    </td>
                    <td class="px-6 py-3.5"><x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge></td>
                    <td class="px-6 py-3.5">
                        @if ($payment->isPending())
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.subscription-payments.approve', $payment) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100">{{ __('ui.approve') }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.subscription-payments.reject', $payment) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 transition hover:bg-rose-100">{{ __('ui.reject') }}</button>
                                </form>
                            </div>
                        @else
                            <span class="block text-end text-xs text-gray-400">{{ $payment->reviewer?->name }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $payments->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: review cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($payments as $payment)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $payment->client->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $payment->plan->name }} · {{ $payment->channel->label() }}</p>
                        </div>
                        <span class="shrink-0 font-semibold tabular-nums text-gray-900">{{ number_format((float) $payment->amount, 2) }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge>
                        <span class="font-mono text-gray-500" dir="ltr">{{ $payment->reference }}</span>
                        @if ($payment->receipt_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank" class="font-medium text-indigo-600">{{ __('ui.receipt') }}</a>
                        @endif
                    </div>
                    @if ($payment->isPending())
                        <div class="mt-3 flex items-center gap-2 border-t border-gray-100 pt-3">
                            <form method="POST" action="{{ route('admin.subscription-payments.approve', $payment) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-emerald-50 py-2 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100">{{ __('ui.approve') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.subscription-payments.reject', $payment) }}" class="flex-1"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-rose-50 py-2 text-xs font-medium text-rose-700 transition hover:bg-rose-100">{{ __('ui.reject') }}</button>
                            </form>
                        </div>
                    @elseif ($payment->reviewer)
                        <p class="mt-3 border-t border-gray-100 pt-3 text-xs text-gray-400">{{ $payment->reviewer->name }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $payments->links() }}</div>
    @endif
</x-layouts.app>
