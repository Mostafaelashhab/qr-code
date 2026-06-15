<x-layouts.app :title="__('ui.platform_reports')">
    <x-page-header :title="__('ui.platform_reports')" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('admin.dashboard')],
        ['label' => __('ui.platform_reports')],
    ]" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="chart" color="indigo" :label="__('ui.active_subscriptions')" :value="$stats['active_subscriptions']" />
        <x-stat-card icon="cash" color="emerald" :label="__('ui.subscription_revenue')" :value="number_format($stats['subscription_revenue'], 2)" />
        <x-stat-card icon="users" color="sky" :label="__('ui.active_clients')" :value="$stats['active_clients']" />
        <x-stat-card icon="users" color="gray" :label="__('ui.inactive_clients')" :value="$stats['inactive_clients']" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-semibold tracking-tight text-gray-900">{{ __('ui.per_plan') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-start">{{ __('ui.plan') }}</th>
                            <th class="px-6 py-3 text-start">{{ __('ui.active_subscriptions') }}</th>
                            <th class="px-6 py-3 text-end">{{ __('ui.revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($revenueByPlan as $plan)
                            <tr class="transition hover:bg-gray-50/70">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $plan->name }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $plan->active_count }}</span>
                                </td>
                                <td class="px-6 py-3 text-end font-semibold tabular-nums text-gray-900">{{ number_format((float) $plan->active_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-card :title="__('ui.expiring_30_days')">
            <ul class="divide-y divide-gray-100">
                @forelse ($expiringSoon as $subscription)
                    <li class="flex items-center justify-between gap-3 py-3 text-sm">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $subscription->client->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $subscription->plan->name }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-gray-500">{{ $subscription->ends_at->isoFormat('LL') }}</span>
                    </li>
                @empty
                    <li class="py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
