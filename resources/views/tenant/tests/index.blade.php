<x-layouts.app :title="__('ui.online_tests')">
    <div class="mb-6 flex items-center justify-end">
        <x-button :href="route('tenant.tests.create')">{{ __('ui.new_test') }}</x-button>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.title') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.group') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.questions') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.attempts') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.status') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tests as $test)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('tenant.tests.show', $test) }}" class="font-medium text-indigo-600 hover:underline">{{ $test->title }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $test->group->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $test->questions_count }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $test->attempts_count }}</td>
                            <td class="px-6 py-4">
                                <x-badge :color="$test->is_published ? 'emerald' : 'gray'">
                                    {{ $test->is_published ? __('ui.published') : __('ui.draft') }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <a href="{{ route('tenant.tests.show', $test) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.view') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state :action-label="__('ui.new_test')" :action-href="route('tenant.tests.create')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $tests->links() }}</div>
</x-layouts.app>
