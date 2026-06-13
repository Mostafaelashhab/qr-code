<x-layouts.app :title="__('ui.plans')">
    <div class="mb-6 flex items-center justify-end">
        <x-button :href="route('admin.plans.create')">{{ __('ui.new_plan') }}</x-button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($plans as $plan)
            <x-card>
                <div class="flex items-start justify-between">
                    <h3 class="font-semibold">{{ $plan->name }}</h3>
                    <x-badge :color="$plan->is_active ? 'emerald' : 'gray'">
                        {{ $plan->is_active ? __('ui.active') : __('ui.inactive') }}
                    </x-badge>
                </div>

                <p class="mt-3 text-2xl font-semibold">
                    {{ number_format((float) $plan->price, 2) }}
                    <span class="text-sm font-normal text-gray-500">/ {{ $plan->billing_period->label() }}</span>
                </p>

                <ul class="mt-4 space-y-1 text-sm text-gray-600">
                    <li>{{ __('ui.max_users') }}: {{ $plan->max_users ?? __('ui.unlimited') }}</li>
                    @foreach (($plan->features ?? []) as $feature)
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500">✓</span>{{ \App\Enums\Feature::tryFrom($feature)?->label() ?? $feature }}
                        </li>
                    @endforeach
                </ul>

                <div class="mt-5 flex items-center justify-between text-sm">
                    <span class="text-xs text-gray-400">{{ $plan->subscriptions_count }} {{ __('ui.subscriptions') }}</span>
                    <a href="{{ route('admin.plans.edit', $plan) }}" class="font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                </div>
            </x-card>
        @empty
            <p class="text-sm text-gray-500">{{ __('ui.no_results') }}</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $plans->links() }}</div>
</x-layouts.app>
