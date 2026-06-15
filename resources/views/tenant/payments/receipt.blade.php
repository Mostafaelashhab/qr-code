<x-layouts.print :title="__('ui.payment_receipt')">
    <div class="flex items-start justify-between border-b border-gray-100 pb-6">
        <div class="flex items-center gap-3">
            @if ($payment->client->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($payment->client->logo_path) }}" alt="" class="size-12 rounded-lg object-cover ring-1 ring-gray-200">
            @endif
            <div>
                <p class="text-lg font-bold tracking-tight">{{ $payment->client->name }}</p>
                <p class="text-sm text-gray-500">{{ __('ui.payment_receipt') }}</p>
            </div>
        </div>
        <div class="text-end text-sm">
            <p class="font-medium">{{ __('ui.receipt_no') }}{{ $payment->id }}</p>
            <p class="text-gray-500">{{ $payment->paid_at->isoFormat('LL') }}</p>
        </div>
    </div>

    <dl class="space-y-3 py-6 text-sm">
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.student') }}</dt><dd class="font-medium">{{ $payment->student->name }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.group') }}</dt><dd>{{ $payment->group?->name ?? '—' }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.method') }}</dt><dd>{{ $payment->method->label() }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.for_month') }}</dt><dd>{{ $payment->for_month ?? '—' }}</dd></div>
        @if ($payment->note)
            <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.notes') }}</dt><dd>{{ $payment->note }}</dd></div>
        @endif
    </dl>

    <div class="flex items-center justify-between border-t border-gray-100 pt-6">
        <span class="text-sm font-medium text-gray-500">{{ __('ui.total') }}</span>
        <span class="text-2xl font-bold tracking-tight tabular-nums">{{ number_format((float) $payment->amount, 2) }} <span class="text-base font-semibold text-gray-400">{{ $payment->client->currency }}</span></span>
    </div>
</x-layouts.print>
