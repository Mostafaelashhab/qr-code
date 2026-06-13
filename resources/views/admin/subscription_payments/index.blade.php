@php use App\Enums\PaymentRequestStatus; @endphp

<x-layouts.app :title="__('ui.subscription_payments')">
    <form method="GET" class="mb-6">
        <select name="status" onchange="this.form.submit()"
                class="rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            <option value="">{{ __('ui.all') }}</option>
            @foreach (PaymentRequestStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </form>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.center') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.plan') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.amount') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.payment_channel') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.transfer_reference') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.status') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.review') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $payment->client->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $payment->plan->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $payment->channel->label() }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-gray-700">{{ $payment->reference }}</span>
                                @if ($payment->receipt_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank" class="ms-2 text-xs text-indigo-600 hover:underline">{{ __('ui.receipt') }}</a>
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge></td>
                            <td class="px-6 py-4">
                                @if ($payment->isPending())
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.subscription-payments.approve', $payment) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-emerald-600 hover:underline">{{ __('ui.approve') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.subscription-payments.reject', $payment) }}"
                                              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.reject') }}</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">{{ $payment->reviewer?->name }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $payments->links() }}</div>
</x-layouts.app>
