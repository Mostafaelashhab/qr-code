@props([
    'href' => null,
    'danger' => false,  // red styling for destructive actions
])

@php
    $classes = 'flex w-full items-center gap-2.5 px-3 py-2 text-start text-sm transition '
        . ($danger ? 'text-rose-600 hover:bg-rose-50' : 'text-gray-700 hover:bg-gray-50');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    {{-- Renders a <button> so it can live inside a <form> (e.g. delete with @method('DELETE')). --}}
    <button type="{{ $attributes->get('type', 'submit') }}" {{ $attributes->except('type')->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
