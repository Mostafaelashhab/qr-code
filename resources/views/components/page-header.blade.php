@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],   // [['label' => .., 'url' => ..], ...] — last item is the current page
])

{{-- Standard page heading: optional breadcrumbs, title, subtitle, and a trailing
     <x-slot:actions> for primary buttons. Used at the top of every page. --}}
<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if (! empty($breadcrumbs))
        <x-breadcrumbs :items="$breadcrumbs" class="mb-2" />
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <h1 class="truncate text-xl font-bold tracking-tight text-gray-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>
</div>
