@php
    $canBill = auth()->user()->client?->hasFeature(\App\Enums\Feature::Payments);
    $canAttend = auth()->user()->client?->hasFeature(\App\Enums\Feature::Attendance);

    // Net balance for a row: (charged − discount) − paid.
    $balanceOf = fn ($s): float => ((float) $s->charged_total - (float) $s->discount_total) - (float) $s->paid_total;
    $initialOf = fn ($s): string => (string) \Illuminate\Support\Str::of($s->name)->trim()->substr(0, 1)->upper();
@endphp

<x-layouts.app :title="__('ui.students')">
    <x-page-header :title="__('ui.students')" :subtitle="number_format($students->total()).' '.__('ui.students')">
        <x-slot:actions>
            <form method="GET" class="relative w-full sm:w-56">
                <svg class="pointer-events-none absolute inset-y-0 start-3 my-auto size-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" /><path d="M21 21l-4.35-4.35" />
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.search_placeholder') }}"
                       class="block w-full rounded-xl border-0 bg-white py-2.5 ps-9 pe-3 text-sm shadow-sm ring-1 ring-inset ring-gray-200 transition focus:ring-2 focus:ring-indigo-600">
            </form>

            @if ($canAttend)
                <x-button variant="secondary" :href="route('tenant.attendance.cards')">{{ __('ui.qr_cards') }}</x-button>
            @endif
            <x-button variant="secondary" :href="route('tenant.students.import')">{{ __('ui.import') }}</x-button>
            <x-button variant="secondary" :href="route('tenant.exports.students')">{{ __('ui.export_csv') }}</x-button>
            <x-button :href="route('tenant.students.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_student') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($students->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="request('search') ? null : __('ui.new_student')"
                           :action-href="request('search') ? null : route('tenant.students.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.name') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.stage') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.phone') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.groups') }}</th>
                @if ($canBill)<th class="px-6 py-3.5 text-start">{{ __('ui.balance') }}</th>@endif
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($students as $student)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <a href="{{ route('tenant.students.show', $student) }}" class="group flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($student) }}</span>
                            <span class="font-medium text-gray-900 group-hover:text-indigo-600">{{ $student->name }}</span>
                        </a>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $student->stage ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-gray-600 tabular-nums" dir="ltr">{{ $student->phone ?? '—' }}</td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $student->enrollments_count }}</span>
                    </td>
                    @if ($canBill)
                        @php $bal = $balanceOf($student); @endphp
                        <td class="px-6 py-3.5">
                            @if ($bal > 0)
                                <span class="font-semibold tabular-nums text-rose-600">{{ number_format($bal, 2) }}</span>
                            @else
                                <x-badge color="emerald">{{ __('ui.paid_up') }}</x-badge>
                            @endif
                        </td>
                    @endif
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('tenant.students.edit', $student) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                            <form method="POST" action="{{ route('tenant.students.destroy', $student) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $students->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($students as $student)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('tenant.students.show', $student) }}" class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($student) }}</span>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $student->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $student->stage ?? '—' }} · <span dir="ltr">{{ $student->phone ?? '—' }}</span></p>
                            </div>
                        </a>
                        @if ($canBill)
                            @php $bal = $balanceOf($student); @endphp
                            @if ($bal > 0)
                                <span class="shrink-0 text-sm font-semibold tabular-nums text-rose-600">{{ number_format($bal, 2) }}</span>
                            @else
                                <x-badge color="emerald">{{ __('ui.paid_up') }}</x-badge>
                            @endif
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                        <span class="text-gray-500">{{ __('ui.groups') }}: <span class="font-medium tabular-nums text-gray-700">{{ $student->enrollments_count }}</span></span>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('tenant.students.edit', $student) }}" class="font-medium text-indigo-600">{{ __('ui.edit') }}</a>
                            <form method="POST" action="{{ route('tenant.students.destroy', $student) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-medium text-rose-600">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $students->links() }}</div>
    @endif
</x-layouts.app>
