@props(['route', 'icon' => 'grid', 'active' => null])

@php
    $isActive = request()->routeIs($active ?? $route);

    $icons = [
        'grid'      => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'dashboard' => 'M4 13h6V4H4zM14 21h6v-9h-6zM14 8h6V4h-6zM4 21h6v-4H4z',
        'building'  => 'M3 21h18M5 21V5a1 1 0 011-1h7a1 1 0 011 1v16M9 7h2M9 11h2M9 15h2M14 21V9h4a1 1 0 011 1v11',
        'tag'       => 'M20 12l-8 8-9-9V4h7zM7.5 7.5h.01',
        'card'      => 'M2 8h20M2 6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2z',
        'users'     => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
        'group'     => 'M17 21v-2a4 4 0 00-3-3.87M9 21v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M12 3a4 4 0 100 8 4 4 0 000-8z',
        'teacher'   => 'M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5',
        'calendar'  => 'M3 9h18M7 3v3M17 3v3M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z',
        'cash'      => 'M2 7h20v10H2zM12 9.5a2.5 2.5 0 100 5 2.5 2.5 0 000-5zM5 10h.01M19 14h.01',
        'wallet'    => 'M3 7a2 2 0 012-2h11v4M3 7v10a2 2 0 002 2h14a2 2 0 002-2v-6a2 2 0 00-2-2H7M16 13h.01',
        'chart'     => 'M3 3v18h18M7 14l3-3 3 3 4-5',
        'clipboard' => 'M9 4h6a1 1 0 011 1v1h2a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h2V5a1 1 0 011-1zM9 4a1 1 0 011-1h4a1 1 0 011 1',
        'chat'      => 'M21 11.5a8.38 8.38 0 01-8.5 8.5 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7A8.38 8.38 0 0112.5 3a8.38 8.38 0 018.5 8.5z',
        'whatsapp'  => 'M3 21l1.9-5.5A8.5 8.5 0 1112 20.5a8.4 8.4 0 01-4.3-1.2zM8.5 8.5c0 4 3 7 7 7M8.5 8.5c0-.7.5-1.2 1.2-1.2.3 0 .6.2.8.6l.6 1.3-.8.9c.5 1 1.3 1.8 2.3 2.3l.9-.8 1.3.6c.4.2.6.5.6.8 0 .7-.5 1.2-1.2 1.2',
        'shield'    => 'M12 3l8 3v6c0 4.5-3.2 7.8-8 9-4.8-1.2-8-4.5-8-9V6z',
        'activity'  => 'M22 12h-4l-3 9L9 3l-3 9H2',
        'settings'  => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z',
    ];
@endphp

<a href="{{ route($route) }}" @if ($isActive) aria-current="page" @endif
   {{ $attributes->merge(['class' => 'group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition '
        . ($isActive
            ? 'bg-indigo-50 text-indigo-700 before:absolute before:inset-y-2 before:start-0 before:w-1 before:rounded-full before:bg-indigo-600'
            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900')]) }}>
    <svg class="size-5 shrink-0 transition {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="{{ $icons[$icon] ?? $icons['grid'] }}" />
    </svg>
    <span class="truncate">{{ $slot }}</span>
</a>
