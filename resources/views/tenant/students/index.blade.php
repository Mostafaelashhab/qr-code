@php $canBill = auth()->user()->client?->hasFeature(\App\Enums\Feature::Payments); @endphp

<x-layouts.app :title="__('ui.students')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex items-center gap-2">
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('ui.search_placeholder') }}"
                   class="w-64 max-w-full rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            <x-button type="submit" variant="secondary">{{ __('ui.search') }}</x-button>
        </form>
        <div class="flex gap-2">
            @if (auth()->user()->client?->hasFeature(\App\Enums\Feature::Attendance))
                <x-button variant="secondary" :href="route('tenant.attendance.cards')">{{ __('ui.qr_cards') }}</x-button>
            @endif
            <x-button variant="secondary" :href="route('tenant.students.import')">{{ __('ui.import') }}</x-button>
            <x-button variant="secondary" :href="route('tenant.exports.students')">{{ __('ui.export_csv') }}</x-button>
            <x-button :href="route('tenant.students.create')">{{ __('ui.new_student') }}</x-button>
        </div>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.name') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.stage') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.phone') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.groups') }}</th>
                        @if ($canBill)<th class="px-6 py-3 text-start">{{ __('ui.balance') }}</th>@endif
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('tenant.students.show', $student) }}" class="font-medium text-indigo-600 hover:underline">{{ $student->name }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->stage ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->enrollments_count }}</td>
                            @if ($canBill)
                                @php $bal = ((float) $student->charged_total - (float) $student->discount_total) - (float) $student->paid_total; @endphp
                                <td class="px-6 py-4">
                                    @if ($bal > 0)
                                        <span class="font-medium text-rose-600 tabular-nums">{{ number_format($bal, 2) }}</span>
                                    @else
                                        <x-badge color="emerald">{{ __('ui.paid_up') }}</x-badge>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4">
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
                    @empty
                        <tr>
                            <td colspan="{{ $canBill ? 6 : 5 }}">
                                <x-empty-state :action-label="request('search') ? null : __('ui.new_student')"
                                               :action-href="request('search') ? null : route('tenant.students.create')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $students->links() }}</div>
</x-layouts.app>
