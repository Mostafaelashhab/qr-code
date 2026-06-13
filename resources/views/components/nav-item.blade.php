@props(['route', 'icon' => 'grid', 'active' => null])

@php
    $isActive = request()->routeIs($active ?? $route);

    $icons = [
        'grid' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'building' => 'M3 21h18M5 21V5a1 1 0 011-1h7a1 1 0 011 1v16M9 7h2M9 11h2M9 15h2M14 21V9h4a1 1 0 011 1v11',
        'tag' => 'M20 12l-8 8-9-9V4h7zM7.5 7.5h.01',
        'card' => 'M2 8h20M2 6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2z',
        'users' => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
    ];
@endphp

<a href="{{ route($route) }}" @if ($isActive) aria-current="page" @endif
   {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition '
        . ($isActive ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900')]) }}>
    <svg class="size-5 shrink-0 transition {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}"
         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="{{ $icons[$icon] ?? $icons['grid'] }}" />
    </svg>
    <span class="truncate">{{ $slot }}</span>
</a>
