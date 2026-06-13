<x-layouts.app :title="__('ui.attendance')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ $session->group->name }}</h2>
            <p class="text-sm text-gray-500">{{ $session->session_date->isoFormat('LL') }} · {{ $session->group->subject->name }}</p>
        </div>
        <div class="flex gap-2">
            @if (auth()->user()->client?->hasFeature(\App\Enums\Feature::Messages))
                <form method="POST" action="{{ route('tenant.reminders.absence', $session) }}">
                    @csrf
                    <x-button type="submit" variant="secondary">{{ __('ui.send_absence_reminders') }}</x-button>
                </form>
            @endif
            <x-button variant="secondary" :href="route('tenant.groups.attendance.create', ['group' => $session->group_id, 'date' => $session->session_date->toDateString()])">
                {{ __('ui.edit') }}
            </x-button>
        </div>
    </div>

    <x-card class="overflow-hidden !p-0">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-start">{{ __('ui.student') }}</th>
                    <th class="px-6 py-3 text-end">{{ __('ui.status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($session->attendances as $attendance)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $attendance->student->name }}</td>
                        <td class="px-6 py-4 text-end"><x-badge :color="$attendance->status->color()">{{ $attendance->status->label() }}</x-badge></td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-6">
        <x-button variant="secondary" :href="route('tenant.groups.show', $session->group_id)">{{ __('ui.back') }}</x-button>
    </div>
</x-layouts.app>
