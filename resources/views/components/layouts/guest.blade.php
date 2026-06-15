@props(['title' => null])

@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$title" />
    <x-assets />
</head>
<body class="h-full bg-linear-to-b from-gray-50 to-gray-100 text-gray-900 antialiased">
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 flex flex-col items-center gap-3 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-600 to-violet-600 text-white shadow-sm">
                    <x-app-logo class="size-8" />
                </span>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('ui.app_name') }}</h1>
                <p class="text-sm text-gray-500">{{ __('ui.tagline') }}</p>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200/70">
                {{ $slot }}
            </div>

            <div class="mt-6 flex justify-center">
                <x-language-switcher />
            </div>
        </div>
    </div>
</body>
</html>
