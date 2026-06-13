@props(['color' => 'gray'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '
    . ($colors[$color] ?? $colors['gray'])]) }}>
    {{ $slot }}
</span>
