@props([
    'id',
    'title' => null,
])

{{-- CSS-only modal using the :target pseudo-class (no JS in this app).
     Open with a link to #{{ id }}; close with a link to "#" (or the X / backdrop).
     Pass footer buttons via <x-slot:footer>. --}}
<div id="{{ $id }}" {{ $attributes->merge(['class' => 'fixed inset-0 z-50 hidden items-center justify-center p-4 target:flex']) }}>
    {{-- Backdrop (also a close target) --}}
    <a href="#" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" aria-label="{{ __('ui.close') }}"></a>

    <div class="relative z-10 w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
        @if ($title)
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <h3 class="font-semibold tracking-tight text-gray-900">{{ $title }}</h3>
                <a href="#" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" aria-label="{{ __('ui.close') }}">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12" /></svg>
                </a>
            </div>
        @endif

        <div class="px-5 py-4 text-sm text-gray-600">{{ $slot }}</div>

        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50/60 px-5 py-3">{{ $footer }}</div>
        @endisset
    </div>
</div>
