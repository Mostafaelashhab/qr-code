<x-layouts.app :title="__('ui.clients')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex items-center gap-2">
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('ui.search_placeholder') }}"
                   class="w-64 max-w-full rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            <x-button type="submit" variant="secondary">{{ __('ui.search') }}</x-button>
        </form>
        <x-button :href="route('admin.clients.create')">{{ __('ui.new_client') }}</x-button>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-start text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.name') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.users') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.plan') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.status') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $client->name }}</div>
                                <div class="text-xs text-gray-500">{{ $client->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->users_count }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $client->latestSubscription?->plan?->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <x-badge :color="$client->is_active ? 'emerald' : 'gray'">
                                    {{ $client->is_active ? __('ui.active') : __('ui.inactive') }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-indigo-600 hover:underline">{{ __('ui.view') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $clients->links() }}</div>
</x-layouts.app>
