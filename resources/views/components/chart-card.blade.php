@props([
    'title' => null,
    'subtitle' => null,
    'value' => null,   // optional headline figure shown on the trailing edge
])

{{-- Card tuned for charts: title + subtitle on one side, an optional headline value
     or <x-slot:actions> on the other. Body slot holds the chart (e.g. x-bar-chart). --}}
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70']) }}>
    <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-6 py-4">
        <div class="min-w-0">
            @if ($title)
                <h2 class="truncate font-semibold tracking-tight text-gray-900">{{ $title }}</h2>
            @endif
            @if ($subtitle)
                <p class="mt-0.5 truncate text-xs text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
        @isset($actions)
            <div class="shrink-0">{{ $actions }}</div>
        @elseif ($value !== null)
            <span class="shrink-0 text-lg font-bold tabular-nums text-gray-900">{{ $value }}</span>
        @endisset
    </div>
    <div class="p-6">{{ $slot }}</div>
</div>
