<x-layouts.app :title="__('ui.subjects')">
    <div class="mb-6 flex items-center justify-end">
        <x-button :href="route('tenant.subjects.create')">{{ __('ui.new_subject') }}</x-button>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.name') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.stage') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.groups') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.status') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($subjects as $subject)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $subject->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $subject->stage ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $subject->groups_count }}</td>
                            <td class="px-6 py-4">
                                <x-badge :color="$subject->is_active ? 'emerald' : 'gray'">
                                    {{ $subject->is_active ? __('ui.active') : __('ui.inactive') }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('tenant.subjects.edit', $subject) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                                    <form method="POST" action="{{ route('tenant.subjects.destroy', $subject) }}"
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

    <div class="mt-4">{{ $subjects->links() }}</div>
</x-layouts.app>
