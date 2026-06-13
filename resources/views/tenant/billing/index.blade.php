@php
    use App\Enums\PaymentChannel;
    $planOptions = $plans->mapWithKeys(fn ($p) => [$p->id => $p->name.' — '.number_format((float) $p->price, 2).' / '.$p->billing_period->label()])->all();
    $channelOptions = collect($channels)->mapWithKeys(fn (PaymentChannel $c) => [$c->value => $c->label()])->all();
@endphp

<x-layouts.app :title="__('ui.billing')">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card :title="__('ui.pay_for_plan')">
                <p class="mb-4 text-sm text-gray-500">{{ __('billing.pay_instructions') }}</p>

                <div class="mb-5 space-y-2">
                    @foreach ($channels as $channel)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2.5 text-sm">
                            <span class="font-medium">{{ $channel->label() }}</span>
                            <span class="font-mono text-gray-700">{{ $channel->receivingAccount() }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mb-5 flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2.5 ring-1 ring-emerald-100">
                    <x-whatsapp-support :label="__('ui.need_help')" />
                </div>

                <form method="POST" action="{{ route('tenant.billing.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @csrf
                    <div class="sm:col-span-2">
                        <x-form.select name="plan_id" :label="__('ui.plan')" :options="$planOptions" :placeholder="'—'" required />
                    </div>
                    <x-form.select name="channel" :label="__('ui.payment_channel')" :options="$channelOptions" :placeholder="'—'" required />
                    <x-form.field name="amount" :label="__('ui.amount')" type="number" step="0.01" min="1" required />
                    <div class="sm:col-span-2">
                        <x-form.field name="reference" :label="__('ui.transfer_reference')" required />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('ui.receipt') }}</label>
                        <input type="file" name="receipt" accept="image/*"
                               class="block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('receipt')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <x-button type="submit">{{ __('ui.submit_payment') }}</x-button>
                    </div>
                </form>
            </x-card>

            <x-card :title="__('ui.my_payments')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <li class="flex items-center justify-between py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $payment->plan->name }} · {{ number_format((float) $payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->channel->label() }} · {{ $payment->reference }} · {{ $payment->created_at->isoFormat('LL') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge>
                                <a href="{{ route('tenant.billing.receipt', $payment) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.print') }}</a>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>
        </div>

        <x-card :title="__('ui.current_subscription')">
            @if ($current)
                <p class="text-xl font-semibold">{{ $current->plan->name }}</p>
                <div class="mt-2"><x-badge :color="$current->status->color()">{{ $current->status->label() }}</x-badge></div>
                <p class="mt-3 text-sm text-gray-500">{{ __('ui.end_date') }}: {{ $current->ends_at?->isoFormat('LL') ?? '∞' }}</p>
            @else
                <p class="text-sm text-gray-500">{{ __('ui.no_active_subscription') }}</p>
            @endif
        </x-card>
    </div>
</x-layouts.app>
