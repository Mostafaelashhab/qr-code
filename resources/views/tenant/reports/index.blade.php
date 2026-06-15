@php
    $reports = [
        ['route' => 'tenant.reports.financial', 'title' => __('ui.financial_report'), 'desc' => __('ui.financial_report_desc'), 'icon' => 'chart', 'color' => 'bg-emerald-50 text-emerald-600'],
        ['route' => 'tenant.reports.attendance', 'title' => __('ui.attendance_report'), 'desc' => __('ui.attendance_report_desc'), 'icon' => 'clipboard', 'color' => 'bg-sky-50 text-sky-600'],
        ['route' => 'tenant.reports.collection', 'title' => __('ui.collection_report'), 'desc' => __('ui.collection_report_desc'), 'icon' => 'cash', 'color' => 'bg-indigo-50 text-indigo-600'],
        ['route' => 'tenant.reports.payroll', 'title' => __('ui.payroll_report'), 'desc' => __('ui.payroll_report_desc'), 'icon' => 'teacher', 'color' => 'bg-violet-50 text-violet-600'],
    ];

    $icons = [
        'chart'     => 'M3 3v18h18M7 14l3-3 3 3 4-5',
        'clipboard' => 'M9 4h6a1 1 0 011 1v1h2a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h2V5a1 1 0 011-1zM9 4a1 1 0 011-1h4a1 1 0 011 1',
        'cash'      => 'M2 7h20v10H2zM12 9.5a2.5 2.5 0 100 5 2.5 2.5 0 000-5zM5 10h.01M19 14h.01',
        'teacher'   => 'M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5',
    ];
@endphp

<x-layouts.app :title="__('ui.reports')">
    <x-page-header :title="__('ui.reports')" :subtitle="__('ui.reports_intro')" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($reports as $report)
            <a href="{{ route($report['route']) }}"
               class="group flex items-start gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $report['color'] }}">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icons[$report['icon']] }}" /></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600">{{ $report['title'] }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $report['desc'] }}</p>
                </div>
                <svg class="mt-1 size-4 shrink-0 text-gray-300 transition group-hover:text-indigo-500 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
            </a>
        @endforeach
    </div>
</x-layouts.app>
