@props(['href' => null, 'variant' => 'primary', 'type' => 'submit'])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-indigo-600',
        'secondary' => 'bg-white text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-gray-400',
        'danger' => 'bg-rose-600 text-white shadow-sm hover:bg-rose-500 focus-visible:outline-rose-600',
    ];

    $classes = 'inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 text-sm font-medium '
        . 'transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 '
        . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
