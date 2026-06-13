<x-layouts.app :title="__('ui.platform_reports')">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card :label="__('ui.active_subscriptions')" :value="$stats['active_subscriptions']" />
        <x-stat-card :label="__('ui.subscription_revenue')" :value="number_format($stats['subscription_revenue'], 2)" />
        <x-stat-card :label="__('ui.active_clients')" :value="$stats['active_clients']" />
        <x-stat-card :label="__('ui.inactive_clients')" :value="$stats['inactive_clients']" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card :title="__('ui.per_plan')">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="py-2 text-start">{{ __('ui.plan') }}</th>
                        <th class="py-2 text-start">{{ __('ui.active_subscriptions') }}</th>
                        <th class="py-2 text-end">{{ __('ui.revenue') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($revenueByPlan as $plan)
                        <tr>
                            <td class="py-3 font-medium text-gray-900">{{ $plan->name }}</td>
                            <td class="py-3 text-gray-600">{{ $plan->active_count }}</td>
                            <td class="py-3 text-end text-gray-600">{{ number_format((float) $plan->active_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        <x-card :title="__('ui.expiring_30_days')">
            <ul class="divide-y divide-gray-100">
                @forelse ($expiringSoon as $subscription)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $subscription->client->name }}</p>
                            <p class="text-xs text-gray-500">{{ $subscription->plan->name }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $subscription->ends_at->isoFormat('LL') }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
