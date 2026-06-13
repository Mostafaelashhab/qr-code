<x-layouts.app :title="__('ui.search_results')">
    <form method="GET" action="{{ route('tenant.search') }}" class="mb-6">
        <input type="search" name="q" value="{{ $query }}" autofocus
               placeholder="{{ __('ui.search_global_placeholder') }}"
               class="block w-full rounded-lg border-0 px-4 py-2.5 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
    </form>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card :title="__('ui.students')">
            <ul class="divide-y divide-gray-100">
                @forelse ($students as $student)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <a href="{{ route('tenant.students.show', $student) }}" class="font-medium text-indigo-600 hover:underline">{{ $student->name }}</a>
                        <span class="text-xs text-gray-500">{{ $student->phone }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        <x-card :title="__('ui.groups')">
            <ul class="divide-y divide-gray-100">
                @forelse ($groups as $group)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <a href="{{ route('tenant.groups.show', $group) }}" class="font-medium text-indigo-600 hover:underline">{{ $group->name }}</a>
                        <span class="text-xs text-gray-500">{{ $group->subject?->name }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
