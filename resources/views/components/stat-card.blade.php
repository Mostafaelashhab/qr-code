@props([
    'label',
    'value',
    'icon' => null,    // optional inline icon key (see $icons below)
    'color' => 'indigo', // tint for the icon chip / accent
    'hint' => null,    // optional small caption under the value
    'href' => null,    // makes the whole card a link (stretched-link pattern)
])

@php
    // Soft chip background + saturated icon colour per accent.
    $palette = [
        'indigo'  => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber'   => 'bg-amber-50 text-amber-600',
        'sky'     => 'bg-sky-50 text-sky-600',
        'violet'  => 'bg-violet-50 text-violet-600',
        'rose'    => 'bg-rose-50 text-rose-600',
        'gray'    => 'bg-gray-100 text-gray-600',
    ];
    $chip = $palette[$color] ?? $palette['indigo'];

    // Self-contained icon set so the card needs no external dependency.
    $icons = [
        'users'   => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 108 0 4 4 0 00-8 0z',
        'group'   => 'M17 21v-2a4 4 0 00-3-3.87M9 21v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M12 3a4 4 0 100 8 4 4 0 000-8z',
        'teacher' => 'M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5',
        'cash'    => 'M2 7h20v10H2zM12 9.5a2.5 2.5 0 100 5 2.5 2.5 0 000-5zM5 10h.01M19 14h.01',
        'chart'   => 'M3 3v18h18M7 14l3-3 3 3 4-5',
        'clock'   => 'M12 7v5l3 2M12 3a9 9 0 100 18 9 9 0 000-18z',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'group relative flex items-start gap-4 overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/70 transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-gray-300/70']) }}>
    @if ($icon)
        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $chip }}">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $icons[$icon] ?? $icons['chart'] }}" />
            </svg>
        </span>
    @endif

    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-medium text-gray-500">{{ $label }}</p>
        <p class="mt-1 text-2xl font-bold tracking-tight tabular-nums text-gray-900 sm:text-3xl">{{ $value }}</p>
        @if ($hint)
            <p class="mt-1 truncate text-xs text-gray-400">{{ $hint }}</p>
        @endif
    </div>

    @if ($href)
        {{-- Stretched link keeps the whole card clickable without nesting interactive elements. --}}
        <a href="{{ $href }}" class="absolute inset-0" aria-label="{{ $label }}"></a>
        <svg class="absolute end-4 top-5 size-4 text-gray-300 transition group-hover:text-gray-400 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 18l6-6-6-6" />
        </svg>
    @endif
</div>
