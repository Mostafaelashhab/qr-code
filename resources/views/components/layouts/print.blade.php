@props(['title' => null])

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
<body class="min-h-full bg-gray-100 text-gray-900 antialiased print:bg-white">
    <div class="mx-auto max-w-2xl px-4 py-10 print:py-0">
        <div class="mb-4 flex justify-end gap-2 print:hidden">
            <a href="{{ url()->previous() }}" class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50">{{ __('ui.back') }}</a>
            <button type="button" onclick="window.print()" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500">{{ __('ui.print') }}</button>
        </div>

        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200 print:rounded-none print:shadow-none print:ring-0">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
