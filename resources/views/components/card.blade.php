@props([
    'title' => null,
    'subtitle' => null, // optional caption under the title
])

{{-- Optional <x-slot:actions> renders on the header's trailing edge (e.g. a "View all" link). --}}
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4">
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
            @endisset
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
