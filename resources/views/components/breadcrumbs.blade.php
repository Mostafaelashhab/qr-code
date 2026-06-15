@props(['items' => []])  {{-- [['label' => .., 'url' => ..], ...]; last item = current --}}

<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500">
        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                @unless ($loop->first)
                    {{-- Chevron flips automatically in RTL --}}
                    <svg class="size-3.5 shrink-0 text-gray-300 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
                @endunless

                @if (! empty($item['url']) && ! $loop->last)
                    <a href="{{ $item['url'] }}" class="transition hover:text-gray-700">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-gray-700" @if ($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
