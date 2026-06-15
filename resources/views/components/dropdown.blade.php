@props([
    'align' => 'end',   // 'start' | 'end' — which edge the menu aligns to (RTL-aware)
    'width' => 'w-48',
])

@php $alignClass = $align === 'start' ? 'start-0' : 'end-0'; @endphp

{{-- CSS-only dropdown built on <details> (this app ships no JS). Pass the trigger
     via <x-slot:trigger> and menu rows (x-menu-item) as the default slot. --}}
<details class="relative inline-block text-start" {{ $attributes }}>
    <summary class="inline-flex cursor-pointer list-none items-center marker:content-[''] [&::-webkit-details-marker]:hidden">
        {{ $trigger }}
    </summary>
    <div class="absolute {{ $alignClass }} z-30 mt-2 {{ $width }} overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-lg ring-1 ring-black/5">
        {{ $slot }}
    </div>
</details>
