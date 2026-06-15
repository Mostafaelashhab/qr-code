@php $canAttend = auth()->user()->client?->hasFeature(\App\Enums\Feature::Attendance); @endphp

<x-layouts.app :title="__('ui.groups')">
    <x-page-header :title="__('ui.groups')" :subtitle="number_format($groups->total()).' '.__('ui.groups')">
        <x-slot:actions>
            <x-button :href="route('tenant.groups.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_group') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($groups->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_group')" :action-href="route('tenant.groups.create')" />
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($groups as $group)
                <div class="group flex flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/70 transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('tenant.groups.show', $group) }}" class="flex min-w-0 items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 006.5 22H20V2H6.5A2.5 2.5 0 004 4.5z" /></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900 group-hover:text-indigo-600">{{ $group->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $group->subject->name }}</p>
                            </div>
                        </a>
                        <x-badge :color="$group->is_active ? 'emerald' : 'gray'">
                            {{ $group->is_active ? __('ui.active') : __('ui.inactive') }}
                        </x-badge>
                    </div>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.teacher') }}</dt><dd class="truncate text-gray-700">{{ $group->teacher?->name ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.schedule') }}</dt><dd class="truncate text-gray-700">{{ $group->schedule ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.monthly_fee') }}</dt><dd class="tabular-nums text-gray-700">{{ number_format((float) $group->monthly_fee, 2) }}</dd></div>
                    </dl>

                    {{-- Enrollment vs capacity --}}
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400">{{ __('ui.enrolled_students') }}</span>
                            <span class="tabular-nums font-medium text-gray-700">{{ $group->enrollments_count }}{{ $group->capacity ? ' / '.$group->capacity : '' }}</span>
                        </div>
                        @if ($group->capacity)
                            @php $fill = min(100, (int) round($group->enrollments_count / max(1, $group->capacity) * 100)); @endphp
                            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full {{ $fill >= 100 ? 'bg-rose-500' : 'bg-indigo-500' }}" style="width: {{ $fill }}%"></div>
                            </div>
                        @endif
                    </div>

                    @if ($canAttend)
                        <div class="mt-auto flex gap-2 border-t border-gray-100 pt-4">
                            <a href="{{ route('tenant.groups.attendance.scan', $group) }}"
                               class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 transition hover:bg-indigo-100">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2M7 12h10" />
                                </svg>
                                {{ __('ui.qr_checkin') }}
                            </a>
                            <a href="{{ route('tenant.groups.attendance.create', $group) }}"
                               class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-xs font-medium text-gray-600 ring-1 ring-gray-200 transition hover:bg-gray-50">
                                {{ __('ui.take_attendance') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $groups->links() }}</div>
    @endif
</x-layouts.app>
