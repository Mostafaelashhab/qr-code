<x-layouts.app :title="$exam->name">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ $exam->name }}</h2>
            <p class="text-sm text-gray-500">
                {{ $exam->group->name }} · {{ $exam->exam_date->isoFormat('LL') }} ·
                {{ __('ui.max_score') }} {{ (int) $exam->max_score }} ·
                {{ __('ui.average') }} {{ $exam->averageScore() !== null ? number_format($exam->averageScore(), 1) : '—' }}
            </p>
        </div>
        <form method="POST" action="{{ route('tenant.exams.destroy', $exam) }}"
              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
            @csrf @method('DELETE')
            <x-button type="submit" variant="danger">{{ __('ui.delete') }}</x-button>
        </form>
    </div>

    <form method="POST" action="{{ route('tenant.grades.store', $exam) }}" class="space-y-6">
        @csrf
        <x-card>
            @forelse ($students as $student)
                <div class="flex items-center justify-between gap-4 border-t border-gray-100 py-3 first:border-t-0">
                    <span class="text-sm font-medium">{{ $student->name }}</span>
                    <input type="number" name="scores[{{ $student->id }}]" step="0.01" min="0" max="{{ $exam->max_score }}"
                           value="{{ $scores[$student->id] ?? '' }}"
                           class="w-28 rounded-lg border-0 px-3 py-1.5 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
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
