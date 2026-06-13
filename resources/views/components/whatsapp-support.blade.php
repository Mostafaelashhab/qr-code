@props([
    'floating' => false,
    'label' => null,
])

@php
    $url = config('support.whatsapp_url');
    $phone = config('support.phone');
    $icon = 'M17.5 14.4c-.3-.1-1.6-.8-1.9-.9-.3-.1-.4-.1-.6.1-.2.3-.7.9-.8 1-.2.2-.3.2-.5.1-.3-.1-1.1-.4-2.1-1.3-.8-.7-1.3-1.5-1.5-1.8-.1-.3 0-.4.1-.5l.4-.5c.1-.2.2-.3.2-.5s0-.4-.1-.5c-.1-.1-.6-1.4-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s.9 2.5 1.1 2.7c.1.2 1.8 2.8 4.4 3.9.6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.6-.6 1.8-1.3.2-.6.2-1.2.2-1.3-.1-.1-.3-.2-.6-.3zM12 2a10 10 0 00-8.5 15.3L2 22l4.8-1.4A10 10 0 1012 2z';
@endphp

@if ($floating)
    <a href="{{ $url }}" target="_blank" rel="noopener"
       class="fixed bottom-5 end-5 z-40 flex size-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg transition hover:bg-emerald-600"
       aria-label="{{ __('ui.contact_support') }}">
        <svg class="size-7" viewBox="0 0 24 24" fill="currentColor"><path d="{{ $icon }}" /></svg>
    </a>
@else
    <a href="{{ $url }}" target="_blank" rel="noopener"
       {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700']) }}>
        <svg class="size-5" viewBox="0 0 24 24" fill="currentColor"><path d="{{ $icon }}" /></svg>
        {{ $label ?? __('ui.contact_support') }} <span dir="ltr" class="text-gray-500">{{ $phone }}</span>
    </a>
@endif
