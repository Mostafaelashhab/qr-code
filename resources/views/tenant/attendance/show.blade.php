<x-layouts.app :title="__('ui.attendance')">
    <x-page-header :title="$session->group->name"
                   :subtitle="$session->session_date->isoFormat('LL').' · '.$session->group->subject->name"
                   :breadcrumbs="[
                       ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
                       ['label' => __('ui.groups'), 'url' => route('tenant.groups.index')],
                       ['label' => $session->group->name, 'url' => route('tenant.groups.show', $session->group_id)],
                       ['label' => $session->session_date->isoFormat('LL')],
                   ]">
        <x-slot:actions>
            @if (auth()->user()->client?->hasFeature(\App\Enums\Feature::Messages))
                <form method="POST" action="{{ route('tenant.reminders.absence', $session) }}">
                    @csrf
                    <x-button type="submit" variant="secondary">{{ __('ui.send_absence_reminders') }}</x-button>
                </form>
            @endif
            <x-button variant="secondary" :href="route('tenant.groups.attendance.create', ['group' => $session->group_id, 'date' => $session->session_date->toDateString()])">
                {{ __('ui.edit') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Status summary (counts per status, from the already-loaded collection) --}}
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach (\App\Enums\AttendanceStatus::cases() as $st)
            @php $count = $session->attendances->filter(fn ($a) => $a->status === $st)->count(); @endphp
            @if ($count > 0)
                <x-badge :color="$st->color()">{{ $st->label() }}: {{ $count }}</x-badge>
            @endif
        @endforeach
    </div>

    <x-data-table>
        <x-slot:head>
            <th class="px-6 py-3.5 text-start">{{ __('ui.student') }}</th>
            <th class="px-6 py-3.5 text-end">{{ __('ui.status') }}</th>
        </x-slot:head>

        @forelse ($session->attendances as $attendance)
            <tr class="transition hover:bg-gray-50/70">
                <td class="px-6 py-3.5 font-medium text-gray-900">{{ $attendance->student->name }}</td>
                <td class="px-6 py-3.5 text-end"><x-badge :color="$attendance->status->color()">{{ $attendance->status->label() }}</x-badge></td>
            </tr>
        @empty
            <tr><td colspan="2" class="px-6 py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-6">
        <x-button variant="secondary" :href="route('tenant.groups.show', $session->group_id)">{{ __('ui.back') }}</x-button>
    </div>
</x-layouts.app>
