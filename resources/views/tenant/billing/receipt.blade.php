<x-layouts.print :title="__('ui.subscription_receipt')">
    <div class="flex items-start justify-between border-b border-gray-100 pb-6">
        <div>
            <p class="text-lg font-bold tracking-tight">{{ $payment->client->name }}</p>
            <p class="text-sm text-gray-500">{{ __('ui.subscription_receipt') }}</p>
        </div>
        <div class="text-end text-sm">
            <p class="font-medium">{{ __('ui.receipt_no') }}{{ $payment->id }}</p>
            <p class="text-gray-500">{{ $payment->created_at->isoFormat('LL') }}</p>
        </div>
    </div>

    <dl class="space-y-3 py-6 text-sm">
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.plan') }}</dt><dd class="font-medium">{{ $payment->plan->name }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.payment_channel') }}</dt><dd>{{ $payment->channel->label() }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.transfer_reference') }}</dt><dd class="font-mono">{{ $payment->reference }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.status') }}</dt><dd><x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge></dd></div>
    </dl>

    <div class="flex items-center justify-between border-t border-gray-100 pt-6">
        <span class="text-sm font-medium text-gray-500">{{ __('ui.total') }}</span>
        <span class="text-2xl font-bold tracking-tight tabular-nums">{{ number_format((float) $payment->amount, 2) }}</span>
    </div>
</x-layouts.print>
