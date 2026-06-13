@props(['label', 'value'])

<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/70 transition hover:shadow-md">
    <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
    <p class="mt-1.5 text-3xl font-semibold tracking-tight tabular-nums">{{ $value }}</p>
</div>
