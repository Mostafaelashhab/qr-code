@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$test->title" :noindex="true" />
    <x-assets />
</head>
<body class="min-h-full bg-gray-50 text-gray-900 antialiased">
    <div class="mx-auto flex min-h-full max-w-lg items-center px-4 py-10">
        <div class="w-full text-center">
            <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14M22 4L12 14.01l-3-3"/></svg>
            </div>
            <h1 class="text-xl font-semibold">{{ __('ui.test_submitted') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $attempt->student->name }} · {{ $test->title }}</p>

            @if ($test->show_results)
                <div class="mx-auto mt-6 max-w-xs rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">{{ __('ui.your_score') }}</p>
                    <p class="mt-1 text-4xl font-bold tabular-nums">{{ (float) $attempt->score }}<span class="text-xl text-gray-400">/{{ (float) $attempt->max_score }}</span></p>
                    <p class="mt-1 text-lg font-semibold text-indigo-600">{{ $attempt->percentage() }}%</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
