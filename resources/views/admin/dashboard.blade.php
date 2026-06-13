<x-layouts.app :title="__('ui.dashboard')">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card :label="__('ui.total_clients')" :value="$stats['clients']" />
        <x-stat-card :label="__('ui.active_clients')" :value="$stats['active_clients']" />
        <x-stat-card :label="__('ui.total_plans')" :value="$stats['plans']" />
        <x-stat-card :label="__('ui.active_subscriptions')" :value="$stats['active_subscriptions']" />
    </div>

    <div class="mt-6">
        <x-card :title="__('ui.recent_clients')">
            <x-bar-chart :data="$clientsByMonth" color="indigo" />
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card :title="__('ui.recent_clients')">
            <ul class="divide-y divide-gray-100">
                @forelse ($recentClients as $client)
                    <li class="flex items-center justify-between py-3">
                        <div>
                            <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-indigo-600 hover:underline">
                                {{ $client->name }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $client->latestSubscription?->plan?->name ?? __('ui.no_active_subscription') }}</p>
                        </div>
                        <x-badge :color="$client->is_active ? 'emerald' : 'gray'">
                            {{ $client->is_active ? __('ui.active') : __('ui.inactive') }}
                        </x-badge>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        <x-card :title="__('ui.expiring_soon')">
            <ul class="divide-y divide-gray-100">
                @forelse ($expiringSoon as $subscription)
                    <li class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-medium">{{ $subscription->client->name }}</p>
                            <p class="text-xs text-gray-500">{{ $subscription->plan->name }}</p>
                        </div>
                        <span class="text-sm text-gray-600">{{ $subscription->ends_at->isoFormat('LL') }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
