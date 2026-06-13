@props([
    'data' => [],      // ['label' => numericValue, ...]
    'color' => 'indigo',
])

@php
    $values = array_values($data);
    $max = count($values) ? max($values) : 0;
    $bar = ['indigo' => 'bg-indigo-500', 'emerald' => 'bg-emerald-500', 'amber' => 'bg-amber-500'][$color] ?? 'bg-indigo-500';
@endphp

<div class="flex h-44 items-end gap-2 sm:gap-3">
    @forelse ($data as $label => $value)
        @php $pct = $max > 0 ? max(4, (int) round($value / $max * 100)) : 4; @endphp
        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1.5">
            <span class="text-[11px] font-medium tabular-nums text-gray-600">{{ $value > 999 ? number_format($value / 1000, 1).'k' : number_format($value, 0) }}</span>
            <div class="w-full rounded-t-md {{ $bar }} opacity-80 transition group-hover:opacity-100" style="height: {{ $pct }}%"></div>
            <span class="truncate text-[11px] text-gray-400">{{ $label }}</span>
        </div>
    @empty
        <p class="m-auto text-sm text-gray-400">{{ __('ui.no_results') }}</p>
    @endforelse
</div>
