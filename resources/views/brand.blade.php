@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';

    $logos = [
        ['file' => 'mark.svg', 'label' => 'Icon mark', 'dark' => false],
        ['file' => 'mark-mono.svg', 'label' => 'Icon — mono', 'dark' => false],
        ['file' => 'logo-horizontal.svg', 'label' => 'Horizontal', 'dark' => false],
        ['file' => 'logo-horizontal-light.svg', 'label' => 'Horizontal — light (dark bg)', 'dark' => true],
        ['file' => 'logo-stacked.svg', 'label' => 'Stacked', 'dark' => false],
        ['file' => 'avatar.svg', 'label' => 'Social avatar', 'dark' => false],
    ];

    $palette = [
        ['name' => 'Indigo', 'hex' => '#4f46e5'],
        ['name' => 'Indigo light', 'hex' => '#6366f1'],
        ['name' => 'Ink', 'hex' => '#1e1b4b'],
        ['name' => 'Muted', 'hex' => '#6b7280'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta title="Brand kit" :noindex="true" />
    <x-assets />
</head>
<body class="min-h-full bg-gray-50 text-gray-900 antialiased">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
        <header class="mb-10 flex items-center gap-3">
            <img src="/brand/mark.svg" alt="" class="size-12">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Sanater · {{ __('ui.app_name') }}</h1>
                <p class="text-sm text-gray-500">Brand kit — logos &amp; colors</p>
            </div>
        </header>

        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Logos</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($logos as $logo)
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex h-40 items-center justify-center p-6 {{ $logo['dark'] ? 'bg-indigo-700' : 'bg-gray-50' }}">
                        <img src="/brand/{{ $logo['file'] }}" alt="{{ $logo['label'] }}" class="max-h-24 max-w-full">
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-sm font-medium">{{ $logo['label'] }}</span>
                        <a href="/brand/{{ $logo['file'] }}" download class="text-xs font-medium text-indigo-600 hover:underline">↓ SVG</a>
                    </div>
                </div>
            @endforeach
        </div>

        <h2 class="mb-4 mt-12 text-sm font-semibold uppercase tracking-wide text-gray-500">Colors</h2>
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
            @foreach ($palette as $color)
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70">
                    <div class="h-24" style="background: {{ $color['hex'] }}"></div>
                    <div class="px-4 py-3">
                        <p class="text-sm font-medium">{{ $color['name'] }}</p>
                        <p class="font-mono text-xs uppercase text-gray-500">{{ $color['hex'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-10 text-center text-xs text-gray-400">Keep clear space around the mark equal to its own padding. Don’t recolor or distort the logo.</p>
    </div>
</body>
</html>
