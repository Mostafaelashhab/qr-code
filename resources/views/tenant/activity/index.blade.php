@php
    $actionLabels = [
        'created' => __('ui.action_created'),
        'updated' => __('ui.action_updated'),
        'deleted' => __('ui.action_deleted'),
    ];
    $actionColors = ['created' => 'emerald', 'updated' => 'amber', 'deleted' => 'rose'];
@endphp

<x-layouts.app :title="__('ui.activity_log')">
    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.action') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.user') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.when') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <x-badge :color="$actionColors[$activity->action] ?? 'gray'">
                                        {{ $actionLabels[$activity->action] ?? $activity->action }}
                                    </x-badge>
                                    <span class="text-gray-700">{{ $activity->description }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $activity->user?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-end text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_activity') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $activities->links() }}</div>
</x-layouts.app>
