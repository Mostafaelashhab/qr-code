@php
    $reports = [
        ['route' => 'tenant.reports.financial', 'title' => __('ui.financial_report'), 'desc' => __('ui.financial_report_desc')],
        ['route' => 'tenant.reports.attendance', 'title' => __('ui.attendance_report'), 'desc' => __('ui.attendance_report_desc')],
        ['route' => 'tenant.reports.collection', 'title' => __('ui.collection_report'), 'desc' => __('ui.collection_report_desc')],
        ['route' => 'tenant.reports.payroll', 'title' => __('ui.payroll_report'), 'desc' => __('ui.payroll_report_desc')],
    ];
@endphp

<x-layouts.app :title="__('ui.reports')">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($reports as $report)
            <a href="{{ route($report['route']) }}" class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-indigo-300">
                <h3 class="font-semibold text-indigo-600">{{ $report['title'] }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ $report['desc'] }}</p>
            </a>
        @endforeach
    </div>
</x-layouts.app>
