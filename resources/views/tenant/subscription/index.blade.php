<x-layouts.app :title="__('ui.my_subscription')">
    <x-page-header :title="__('ui.my_subscription')" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.my_subscription')],
    ]" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.current_subscription')" class="lg:col-span-1">
            @if ($current)
                <p class="text-2xl font-semibold tracking-tight">{{ $current->plan->name }}</p>
                <div class="mt-2"><x-badge :color="$current->status->color()">{{ $current->status->label() }}</x-badge></div>

                @if ($current->daysRemaining() !== null)
                    @php $days = $current->daysRemaining(); @endphp
                    <div class="mt-4 rounded-xl px-4 py-3 {{ $days <= 7 ? 'bg-amber-50 ring-1 ring-amber-100' : 'bg-gray-50' }}">
                        <p class="text-2xl font-bold tabular-nums {{ $days <= 7 ? 'text-amber-700' : 'text-gray-900' }}">{{ $days }}</p>
                        <p class="text-xs text-gray-500">{{ __('ui.days_remaining') }}</p>
                    </div>
                @endif

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.start_date') }}</dt><dd class="text-gray-700">{{ $current->starts_at?->isoFormat('LL') ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.end_date') }}</dt><dd class="text-gray-700">{{ $current->ends_at?->isoFormat('LL') ?? '∞' }}</dd></div>
                </dl>

                @if (auth()->user()->isClientAdmin())
                    <a href="{{ route('tenant.billing.index') }}" class="mt-4 inline-flex text-sm font-medium text-indigo-600 hover:underline">{{ __('ui.billing') }}</a>
                @endif
            @else
                <p class="text-sm text-gray-500">{{ __('ui.no_active_subscription') }}</p>
            @endif
        </x-card>

        <x-card :title="__('ui.subscription_history')" class="lg:col-span-2">
            <ul class="divide-y divide-gray-100">
                @forelse ($history as $subscription)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $subscription->plan->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $subscription->starts_at?->isoFormat('LL') ?? '—' }} → {{ $subscription->ends_at?->isoFormat('LL') ?? '∞' }}
                            </p>
                        </div>
                        <x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
