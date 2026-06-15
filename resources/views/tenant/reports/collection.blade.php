@php
    $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all();
    $currency = auth()->user()->client?->currency;
@endphp

<x-layouts.app :title="__('ui.collection_report')">
    <x-page-header :title="__('ui.collection_report')" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.reports'), 'url' => route('tenant.reports.index')],
        ['label' => __('ui.collection_report')],
    ]" />

    <x-filter-bar>
        <div>
            <label for="group_id" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.group') }}</label>
            <select name="group_id" id="group_id"
                    class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
                @foreach ($groupOptions as $id => $name)
                    <option value="{{ $id }}" @selected($group?->id === $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="month" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}"
                   class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
        </div>
        <x-button type="submit" variant="secondary">{{ __('ui.show') }}</x-button>
    </x-filter-bar>

    @if (! $group)
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.select_group')" />
        </x-card>
    @else
        @php $outstanding = max(0, (float) $expected - (float) $collected); @endphp
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-stat-card icon="cash" color="emerald" :label="__('ui.collected')" :value="number_format($collected, 2)" :hint="$currency" />
            <x-stat-card icon="cash" color="indigo" :label="__('ui.expected')" :value="number_format($expected, 2)" :hint="$currency" />
            <x-stat-card icon="clock" :color="$outstanding > 0 ? 'rose' : 'emerald'" :label="__('ui.outstanding')" :value="number_format($outstanding, 2)" :hint="$currency" />
        </div>

        @if ($unpaid->isNotEmpty() && auth()->user()->client?->hasFeature(\App\Enums\Feature::Messages))
            <div class="mb-6 flex justify-end">
                <form method="POST" action="{{ route('tenant.reminders.payment') }}">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <x-button type="submit" variant="secondary">{{ __('ui.send_payment_reminders') }}</x-button>
                </form>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-card :title="__('ui.unpaid').' ('.$unpaid->count().')'">
                <ul class="divide-y divide-gray-100">
                    @forelse ($unpaid as $student)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <span class="font-medium text-gray-800">{{ $student->name }}</span>
                            <span class="text-xs text-gray-500 tabular-nums" dir="ltr">{{ $student->phone ?? '' }}</span>
                        </li>
                    @empty
                        <li class="py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card :title="__('ui.paid').' ('.$paid->count().')'">
                <ul class="divide-y divide-gray-100">
                    @forelse ($paid as $student)
                        <li class="flex items-center gap-2 py-3 text-sm font-medium text-gray-800">
                            <svg class="size-4 shrink-0 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                            {{ $student->name }}
                        </li>
                    @empty
                        <li class="py-10 text-center text-sm text-gray-400">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    @endif
</x-layouts.app>
