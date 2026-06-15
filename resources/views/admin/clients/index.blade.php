@php $initialOf = fn ($c): string => (string) \Illuminate\Support\Str::of($c->name)->trim()->substr(0, 1)->upper(); @endphp

<x-layouts.app :title="__('ui.clients')">
    <x-page-header :title="__('ui.clients')" :subtitle="number_format($clients->total()).' '.__('ui.clients')">
        <x-slot:actions>
            <form method="GET" class="relative w-full sm:w-64">
                <svg class="pointer-events-none absolute inset-y-0 start-3 my-auto size-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" /><path d="M21 21l-4.35-4.35" />
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.search_placeholder') }}"
                       class="block w-full rounded-xl border-0 bg-white py-2.5 ps-9 pe-3 text-sm shadow-sm ring-1 ring-inset ring-gray-200 transition focus:ring-2 focus:ring-indigo-600">
            </form>
            <x-button :href="route('admin.clients.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_client') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($clients->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="request('search') ? null : __('ui.new_client')"
                           :action-href="request('search') ? null : route('admin.clients.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.name') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.users') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.plan') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.status') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($clients as $client)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($client) }}</span>
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900">{{ $client->name }}</div>
                                <div class="truncate text-xs text-gray-500">{{ $client->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $client->users_count }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $client->latestSubscription?->plan?->name ?? '—' }}</td>
                    <td class="px-6 py-3.5">
                        <x-badge :color="$client->is_active ? 'emerald' : 'gray'">{{ $client->is_active ? __('ui.active') : __('ui.inactive') }}</x-badge>
                    </td>
                    <td class="px-6 py-3.5 text-end">
                        <a href="{{ route('admin.clients.show', $client) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.view') }}</a>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $clients->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($clients as $client)
                <a href="{{ route('admin.clients.show', $client) }}" class="block rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($client) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $client->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $client->latestSubscription?->plan?->name ?? '—' }} · {{ $client->users_count }} {{ __('ui.users') }}</p>
                        </div>
                        <x-badge :color="$client->is_active ? 'emerald' : 'gray'">{{ $client->is_active ? __('ui.active') : __('ui.inactive') }}</x-badge>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $clients->links() }}</div>
    @endif
</x-layouts.app>
