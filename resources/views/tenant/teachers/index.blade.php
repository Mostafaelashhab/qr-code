@php $initialOf = fn ($t): string => (string) \Illuminate\Support\Str::of($t->name)->trim()->substr(0, 1)->upper(); @endphp

<x-layouts.app :title="__('ui.teachers')">
    <x-page-header :title="__('ui.teachers')" :subtitle="number_format($teachers->total()).' '.__('ui.teachers')">
        <x-slot:actions>
            <x-button :href="route('tenant.teachers.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_teacher') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($teachers->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_teacher')" :action-href="route('tenant.teachers.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.name') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.subject') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.phone') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.groups') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($teachers as $teacher)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($teacher) }}</span>
                            <span class="font-medium text-gray-900">{{ $teacher->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $teacher->subject?->name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-gray-600 tabular-nums">{{ $teacher->phone ?? '—' }}</td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $teacher->groups_count }}</span>
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('tenant.teachers.edit', $teacher) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                            <form method="POST" action="{{ route('tenant.teachers.destroy', $teacher) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $teachers->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($teachers as $teacher)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($teacher) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $teacher->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $teacher->subject?->name ?? '—' }} · {{ $teacher->phone ?? '—' }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $teacher->groups_count }} {{ __('ui.groups') }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-4 border-t border-gray-100 pt-3 text-xs">
                        <a href="{{ route('tenant.teachers.edit', $teacher) }}" class="font-medium text-indigo-600">{{ __('ui.edit') }}</a>
                        <form method="POST" action="{{ route('tenant.teachers.destroy', $teacher) }}"
                              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="font-medium text-rose-600">{{ __('ui.delete') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $teachers->links() }}</div>
    @endif
</x-layouts.app>
