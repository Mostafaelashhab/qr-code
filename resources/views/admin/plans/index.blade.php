<x-layouts.app :title="__('ui.plans')">
    <x-page-header :title="__('ui.plans')" :subtitle="number_format($plans->total()).' '.__('ui.plans')">
        <x-slot:actions>
            <x-button :href="route('admin.plans.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_plan') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($plans->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_plan')" :action-href="route('admin.plans.create')" />
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($plans as $plan)
                <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-semibold tracking-tight text-gray-900">{{ $plan->name }}</h3>
                        <x-badge :color="$plan->is_active ? 'emerald' : 'gray'">{{ $plan->is_active ? __('ui.active') : __('ui.inactive') }}</x-badge>
                    </div>

                    <p class="mt-3 text-3xl font-bold tracking-tight tabular-nums text-gray-900">
                        {{ number_format((float) $plan->price, 0) }}
                        <span class="text-sm font-normal text-gray-400">/ {{ $plan->billing_period->label() }}</span>
                    </p>

                    <ul class="mt-4 space-y-1.5 text-sm text-gray-600">
                        <li class="flex items-center gap-2 text-gray-500">
                            <span class="text-gray-400">•</span>{{ __('ui.max_users') }}: {{ $plan->max_users ?? __('ui.unlimited') }}
                        </li>
                        @foreach (($plan->features ?? []) as $feature)
                            <li class="flex items-center gap-2">
                                <svg class="size-4 shrink-0 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                                {{ \App\Enums\Feature::tryFrom($feature)?->label() ?? $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4 text-sm">
                        <span class="text-xs text-gray-400">{{ $plan->subscriptions_count }} {{ __('ui.subscriptions') }}</span>
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $plans->links() }}</div>
    @endif
</x-layouts.app>
