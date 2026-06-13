<x-layouts.app :title="__('ui.my_subscription')">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.current_subscription')" class="lg:col-span-1">
            @if ($current)
                <p class="text-2xl font-semibold">{{ $current->plan->name }}</p>
                <div class="mt-2"><x-badge :color="$current->status->color()">{{ $current->status->label() }}</x-badge></div>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.start_date') }}</dt><dd>{{ $current->starts_at?->isoFormat('LL') ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.end_date') }}</dt><dd>{{ $current->ends_at?->isoFormat('LL') ?? '∞' }}</dd></div>
                    @if ($current->daysRemaining() !== null)
                        <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.days_remaining') }}</dt><dd>{{ $current->daysRemaining() }}</dd></div>
                    @endif
                </dl>
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
