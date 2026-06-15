@php $initialOf = fn ($c): string => (string) \Illuminate\Support\Str::of($c->name)->trim()->substr(0, 1)->upper(); @endphp

<x-layouts.app :title="__('ui.dashboard')">
    <x-page-header :title="__('ui.dashboard')" :subtitle="__('ui.platform_reports')" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="users" color="indigo" :label="__('ui.total_clients')" :value="$stats['clients']" />
        <x-stat-card icon="users" color="emerald" :label="__('ui.active_clients')" :value="$stats['active_clients']" />
        <x-stat-card icon="cash" color="violet" :label="__('ui.total_plans')" :value="$stats['plans']" />
        <x-stat-card icon="chart" color="sky" :label="__('ui.active_subscriptions')" :value="$stats['active_subscriptions']" />
    </div>

    <div class="mt-6">
        <x-chart-card :title="__('ui.clients')" :subtitle="__('ui.last_6_months')">
            <x-bar-chart :data="$clientsByMonth" color="indigo" />
        </x-chart-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card :title="__('ui.recent_clients')">
            <ul class="divide-y divide-gray-100">
                @forelse ($recentClients as $client)
                    <li class="flex items-center gap-3 py-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($client) }}</span>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $client->name }}</a>
                            <p class="truncate text-xs text-gray-500">{{ $client->latestSubscription?->plan?->name ?? __('ui.no_active_subscription') }}</p>
                        </div>
                        <x-badge :color="$client->is_active ? 'emerald' : 'gray'">{{ $client->is_active ? __('ui.active') : __('ui.inactive') }}</x-badge>
                    </li>
                @empty
                    <li class="py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        <x-card :title="__('ui.expiring_soon')">
            <ul class="divide-y divide-gray-100">
                @forelse ($expiringSoon as $subscription)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $subscription->client->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $subscription->plan->name }}</p>
                        </div>
                        <span class="shrink-0 text-sm text-gray-600">{{ $subscription->ends_at->isoFormat('LL') }}</span>
                    </li>
                @empty
                    <li class="py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
