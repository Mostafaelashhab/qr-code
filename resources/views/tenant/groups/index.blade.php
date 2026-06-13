<x-layouts.app :title="__('ui.groups')">
    <div class="mb-6 flex items-center justify-end">
        <x-button :href="route('tenant.groups.create')">{{ __('ui.new_group') }}</x-button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($groups as $group)
            <x-card>
                <div class="flex items-start justify-between">
                    <a href="{{ route('tenant.groups.show', $group) }}" class="font-semibold text-indigo-600 hover:underline">{{ $group->name }}</a>
                    <x-badge :color="$group->is_active ? 'emerald' : 'gray'">
                        {{ $group->is_active ? __('ui.active') : __('ui.inactive') }}
                    </x-badge>
                </div>
                <dl class="mt-4 space-y-1.5 text-sm text-gray-600">
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.subject') }}</dt><dd>{{ $group->subject->name }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.teacher') }}</dt><dd>{{ $group->teacher?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.schedule') }}</dt><dd>{{ $group->schedule ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.enrolled_students') }}</dt><dd>{{ $group->enrollments_count }}{{ $group->capacity ? ' / '.$group->capacity : '' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('ui.monthly_fee') }}</dt><dd>{{ number_format((float) $group->monthly_fee, 2) }}</dd></div>
                </dl>

                @if (auth()->user()->client?->hasFeature(\App\Enums\Feature::Attendance))
                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-4">
                        <a href="{{ route('tenant.groups.attendance.scan', $group) }}"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2M7 12h10" />
                            </svg>
                            {{ __('ui.qr_checkin') }}
                        </a>
                        <a href="{{ route('tenant.groups.attendance.create', $group) }}"
                           class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-xs font-medium text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50">
                            {{ __('ui.take_attendance') }}
                        </a>
                    </div>
                @endif
            </x-card>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 rounded-2xl bg-white ring-1 ring-gray-200/70">
                <x-empty-state :action-label="__('ui.new_group')" :action-href="route('tenant.groups.create')" />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $groups->links() }}</div>
</x-layouts.app>
