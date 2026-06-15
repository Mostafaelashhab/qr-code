@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$title" :noindex="true" />
    <x-assets />
</head>
<body class="h-full bg-linear-to-b from-gray-50 to-gray-100 text-gray-900 antialiased">
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-12 text-center">
        <span class="flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-600 to-violet-600 text-white shadow-sm">
            <x-app-logo class="size-9" />
        </span>

        <p class="mt-8 text-6xl font-bold tracking-tight tabular-nums text-gray-900">{{ $code }}</p>
        <h1 class="mt-2 text-lg font-semibold">{{ $title }}</h1>
        <p class="mt-1 max-w-sm text-sm text-gray-500">{{ $message }}</p>

        <a href="{{ url('/') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
            <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>
            {{ __('errors.back_home') }}
        </a>
    </div>
</body>
</html>
