@php $initialOf = fn ($s): string => (string) \Illuminate\Support\Str::of($s->name)->trim()->substr(0, 1)->upper(); @endphp

<x-layouts.app :title="$exam->name">
    <x-page-header :title="$exam->name"
                   :subtitle="$exam->group->name.' · '.$exam->exam_date->isoFormat('LL').' · '.__('ui.max_score').' '.(int) $exam->max_score.' · '.__('ui.average').' '.($exam->averageScore() !== null ? number_format($exam->averageScore(), 1) : '—')"
                   :breadcrumbs="[
                       ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
                       ['label' => __('ui.groups'), 'url' => route('tenant.groups.index')],
                       ['label' => $exam->group->name, 'url' => route('tenant.groups.show', $exam->group_id)],
                       ['label' => $exam->name],
                   ]">
        <x-slot:actions>
            <form method="POST" action="{{ route('tenant.exams.destroy', $exam) }}"
                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger">{{ __('ui.delete') }}</x-button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('tenant.grades.store', $exam) }}" class="space-y-6">
        @csrf
        <x-card>
            @forelse ($students as $student)
                <div class="flex items-center justify-between gap-4 border-t border-gray-100 py-3 first:border-t-0">
                    <div class="flex items-center gap-2.5">
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600">{{ $initialOf($student) }}</span>
                        <span class="text-sm font-medium text-gray-800">{{ $student->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="scores[{{ $student->id }}]" step="0.01" min="0" max="{{ $exam->max_score }}"
                               value="{{ $scores[$student->id] ?? '' }}"
                               class="w-24 rounded-lg border-0 px-3 py-1.5 text-end text-sm tabular-nums ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        <span class="text-xs text-gray-400 tabular-nums">/ {{ (int) $exam->max_score }}</span>
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm text-gray-500">{{ __('ui.no_enrolled_students') }}</p>
            @endforelse
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button variant="secondary" :href="route('tenant.groups.show', $exam->group_id)">{{ __('ui.back') }}</x-button>
            @if ($students->isNotEmpty())
                <x-button type="submit">{{ __('ui.save_grades') }}</x-button>
            @endif
        </div>
    </form>
</x-layouts.app>
