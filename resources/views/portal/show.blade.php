@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$student->name" :noindex="true" />
    <x-assets />
</head>
<body class="min-h-full bg-gray-50 text-gray-900 antialiased">
    {{-- Header --}}
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-3xl items-center gap-3 px-4 py-4 sm:px-6">
            @if ($client->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo_path) }}" alt="" class="size-10 rounded-lg object-cover ring-1 ring-gray-200">
            @else
                <span class="flex size-10 items-center justify-center rounded-lg bg-indigo-600 text-white"><x-app-logo class="size-6" /></span>
            @endif
            <div>
                <p class="font-semibold tracking-tight">{{ $client->name }}</p>
                <p class="text-xs text-gray-500">{{ __('portal.title') }}</p>
            </div>
            <div class="ms-auto"><x-language-switcher /></div>
        </div>
    </header>

    <main class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $student->name }}</h1>
            <p class="text-sm text-gray-500">{{ $student->stage }}</p>
        </div>

        {{-- Summary tiles --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 text-center shadow-sm ring-1 ring-gray-200/70">
                <p class="text-sm text-gray-500">{{ __('ui.attendance_rate') }}</p>
                <p class="mt-1 text-3xl font-bold tabular-nums">{{ $attendanceRate !== null ? $attendanceRate.'%' : '—' }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 text-center shadow-sm ring-1 ring-gray-200/70">
                <p class="text-sm text-gray-500">{{ __('ui.balance') }}</p>
                <p class="mt-1 text-3xl font-bold tabular-nums {{ $balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format(abs($balance), 0) }}</p>
                @if ($balance <= 0)<p class="text-xs text-emerald-600">{{ __('ui.paid_up') }}</p>@endif
            </div>
            <div class="rounded-2xl bg-white p-5 text-center shadow-sm ring-1 ring-gray-200/70">
                <p class="text-sm text-gray-500">{{ __('ui.total_paid') }}</p>
                <p class="mt-1 text-3xl font-bold tabular-nums">{{ number_format($totalPaid, 0) }}</p>
            </div>
        </div>

        {{-- Groups --}}
        <x-card :title="__('ui.groups')">
            <ul class="divide-y divide-gray-100">
                @forelse ($student->groups as $group)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <span class="font-medium">{{ $group->name }}</span>
                        <span class="text-xs text-gray-500">{{ $group->subject->name }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        {{-- Grades --}}
        <x-card :title="__('ui.exams')">
            <ul class="divide-y divide-gray-100">
                @forelse ($grades as $grade)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $grade->exam->name }}</p>
                            <p class="text-xs text-gray-500">{{ $grade->exam->group?->name }} · {{ $grade->exam->exam_date->isoFormat('LL') }}</p>
                        </div>
                        <span class="font-semibold tabular-nums">{{ (float) $grade->score }} / {{ (int) $grade->exam->max_score }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        {{-- Recent attendance --}}
        <x-card :title="__('ui.attendance')">
            <ul class="divide-y divide-gray-100">
                @forelse ($attendances as $attendance)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $attendance->session->group?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $attendance->session->session_date->isoFormat('LL') }}</p>
                        </div>
                        <x-badge :color="$attendance->status->color()">{{ $attendance->status->label() }}</x-badge>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        <p class="pb-4 text-center text-xs text-gray-400">© {{ now()->year }} {{ $client->name }}</p>
    </main>
</body>
</html>
