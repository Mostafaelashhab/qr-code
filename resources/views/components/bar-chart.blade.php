@props([
    'data' => [],      // ['label' => numericValue, ...]
    'color' => 'indigo',
])

@php
    $values = array_values($data);
    $max = count($values) ? max($values) : 0;

    // Gradient fill per accent (Tailwind v4 `bg-linear-*`).
    $palette = [
        'indigo'  => 'from-indigo-600 to-indigo-400',
        'emerald' => 'from-emerald-600 to-emerald-400',
        'amber'   => 'from-amber-500 to-amber-300',
    ];
    $bar = $palette[$color] ?? $palette['indigo'];
@endphp

<div class="flex h-48 items-end gap-2 sm:gap-3">
    @forelse ($data as $label => $value)
        @php $pct = $max > 0 ? max(3, (int) round($value / $max * 100)) : 3; @endphp
        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1.5">
            <span class="text-[11px] font-semibold tabular-nums text-gray-500 transition group-hover:text-gray-900">
                {{ $value > 999 ? number_format($value / 1000, 1).'k' : number_format($value, 0) }}
            </span>
            {{-- Faint track keeps light months legible, gradient bar sits on top. --}}
            <div class="relative flex w-full flex-1 items-end overflow-hidden rounded-md bg-gray-50">
                <div class="w-full rounded-md bg-linear-to-t {{ $bar }} shadow-sm transition-all duration-300 group-hover:brightness-110" style="height: {{ $pct }}%"></div>
            </div>
            <span class="h-4 truncate text-[11px] font-medium text-gray-400">{{ $label }}</span>
        </div>
    @empty
        <p class="m-auto text-sm text-gray-400">{{ __('ui.no_results') }}</p>
    @endforelse
</div>
