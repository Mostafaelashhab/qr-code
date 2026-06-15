<x-layouts.app :title="__('ui.online_tests')">
    <x-page-header :title="__('ui.online_tests')" :subtitle="number_format($tests->total()).' '.__('ui.online_tests')">
        <x-slot:actions>
            <x-button :href="route('tenant.tests.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_test') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($tests->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_test')" :action-href="route('tenant.tests.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.title') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.group') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.questions') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.attempts') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.status') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($tests as $test)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <a href="{{ route('tenant.tests.show', $test) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $test->title }}</a>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $test->group->name }}</td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $test->questions_count }}</span>
                    </td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $test->attempts_count }}</span>
                    </td>
                    <td class="px-6 py-3.5">
                        <x-badge :color="$test->is_published ? 'emerald' : 'gray'">{{ $test->is_published ? __('ui.published') : __('ui.draft') }}</x-badge>
                    </td>
                    <td class="px-6 py-3.5 text-end">
                        <a href="{{ route('tenant.tests.show', $test) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.view') }}</a>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $tests->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($tests as $test)
                <a href="{{ route('tenant.tests.show', $test) }}" class="block rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $test->title }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $test->group->name }}</p>
                        </div>
                        <x-badge :color="$test->is_published ? 'emerald' : 'gray'">{{ $test->is_published ? __('ui.published') : __('ui.draft') }}</x-badge>
                    </div>
                    <div class="mt-3 flex items-center gap-4 border-t border-gray-100 pt-3 text-xs text-gray-500">
                        <span>{{ $test->questions_count }} {{ __('ui.questions') }}</span>
                        <span>{{ $test->attempts_count }} {{ __('ui.attempts') }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $tests->links() }}</div>
    @endif
</x-layouts.app>
