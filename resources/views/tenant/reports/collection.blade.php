@php
    $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all();
@endphp

<x-layouts.app :title="__('ui.collection_report')">
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-2">
        <div>
            <label for="group_id" class="block text-sm font-medium text-gray-700">{{ __('ui.group') }}</label>
            <select name="group_id" id="group_id"
                    class="mt-1 rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                @foreach ($groupOptions as $id => $name)
                    <option value="{{ $id }}" @selected($group?->id === $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="mt-1 rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </form>

    @if (! $group)
        <x-card><p class="text-sm text-gray-500">{{ __('ui.select_group') }}</p></x-card>
    @else
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="grid flex-1 grid-cols-2 gap-4">
                <x-stat-card :label="__('ui.collected')" :value="number_format($collected, 2)" />
                <x-stat-card :label="__('ui.expected')" :value="number_format($expected, 2)" />
            </div>
            @if ($unpaid->isNotEmpty() && auth()->user()->client?->hasFeature(\App\Enums\Feature::Messages))
                <form method="POST" action="{{ route('tenant.reminders.payment') }}">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <x-button type="submit" variant="secondary">{{ __('ui.send_payment_reminders') }}</x-button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-card :title="__('ui.unpaid').' ('.$unpaid->count().')'">
                <ul class="divide-y divide-gray-100">
                    @forelse ($unpaid as $student)
                        <li class="flex items-center justify-between py-3 text-sm">
                            <span class="font-medium">{{ $student->name }}</span>
                            <span class="text-xs text-gray-500">{{ $student->phone ?? '' }}</span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card :title="__('ui.paid').' ('.$paid->count().')'">
                <ul class="divide-y divide-gray-100">
                    @forelse ($paid as $student)
                        <li class="py-3 text-sm font-medium">{{ $student->name }}</li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    @endif
</x-layouts.app>
