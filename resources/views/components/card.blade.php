@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70']) }}>
    @if ($title)
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="font-semibold tracking-tight">{{ $title }}</h2>
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
