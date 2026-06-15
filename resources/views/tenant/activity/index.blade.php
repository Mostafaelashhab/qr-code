@php
    $actionLabels = [
        'created' => __('ui.action_created'),
        'updated' => __('ui.action_updated'),
        'deleted' => __('ui.action_deleted'),
    ];
    $actionColors = ['created' => 'emerald', 'updated' => 'amber', 'deleted' => 'rose'];
@endphp

<x-layouts.app :title="__('ui.activity_log')">
    <x-page-header :title="__('ui.activity_log')" :subtitle="number_format($activities->total()).' '.__('ui.activity_log')" />

    @if ($activities->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.no_activity')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.action') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.user') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.when') }}</th>
            </x-slot:head>

            @foreach ($activities as $activity)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <x-badge :color="$actionColors[$activity->action] ?? 'gray'">{{ $actionLabels[$activity->action] ?? $activity->action }}</x-badge>
                            <span class="text-gray-700">{{ $activity->description }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $activity->user?->name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-end text-xs text-gray-500" title="{{ $activity->created_at->isoFormat('LLL') }}">{{ $activity->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $activities->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: activity feed --}}
        <div class="space-y-3 md:hidden">
            @foreach ($activities as $activity)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center justify-between gap-2">
                        <x-badge :color="$actionColors[$activity->action] ?? 'gray'">{{ $actionLabels[$activity->action] ?? $activity->action }}</x-badge>
                        <span class="text-xs text-gray-400" title="{{ $activity->created_at->isoFormat('LLL') }}">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-700">{{ $activity->description }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $activity->user?->name ?? '—' }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $activities->links() }}</div>
    @endif
</x-layouts.app>
