@props([
    'title' => null,
    'description' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center">
    <span class="flex size-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 13h6m-6 4h6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
        </svg>
    </span>
    <div>
        <p class="font-medium text-gray-900">{{ $title ?? __('ui.no_results') }}</p>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @if ($actionLabel && $actionHref)
        <x-button :href="$actionHref" class="mt-1">{{ $actionLabel }}</x-button>
    @endif
</div>
