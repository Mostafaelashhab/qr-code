<x-layouts.app :title="__('ui.teachers')">
    <div class="mb-6 flex items-center justify-end">
        <x-button :href="route('tenant.teachers.create')">{{ __('ui.new_teacher') }}</x-button>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.name') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.subject') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.phone') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.groups') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($teachers as $teacher)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $teacher->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $teacher->subject?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $teacher->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $teacher->groups_count }}</td>
                            <td class="px-6 py-4">
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
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $teachers->links() }}</div>
</x-layouts.app>
