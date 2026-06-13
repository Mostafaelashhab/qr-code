<x-layouts.app :title="$client->name">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-semibold">{{ $client->name }}</h2>
            <x-badge :color="$client->is_active ? 'emerald' : 'gray'">
                {{ $client->is_active ? __('ui.active') : __('ui.inactive') }}
            </x-badge>
        </div>
        <div class="flex gap-2">
            <x-button variant="secondary" :href="route('admin.clients.edit', $client)">{{ __('ui.edit') }}</x-button>
            <x-button variant="secondary" :href="route('admin.subscriptions.create', ['client_id' => $client->id])">
                {{ __('ui.start_subscription') }}
            </x-button>
            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}"
                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">{{ __('ui.delete') }}</x-button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.client_details')">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.email') }}</dt><dd>{{ $client->email ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.phone') }}</dt><dd>{{ $client->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.address') }}</dt><dd class="text-end">{{ $client->address ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.created_at') }}</dt><dd>{{ $client->created_at->isoFormat('LL') }}</dd></div>
            </dl>
        </x-card>

        <div class="lg:col-span-2 space-y-6">
            <x-card :title="__('ui.subscriptions')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($client->subscriptions->sortByDesc('created_at') as $subscription)
                        <li class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $subscription->plan->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $subscription->starts_at?->isoFormat('LL') ?? '—' }}
                                    →
                                    {{ $subscription->ends_at?->isoFormat('LL') ?? '∞' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge>
                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card :title="__('ui.users')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($client->users as $user)
                        <li class="flex items-center justify-between py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                            <x-badge color="indigo">{{ $user->role->label() }}</x-badge>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>

    <div class="mt-6">
        <x-button variant="secondary" :href="route('admin.clients.index')">{{ __('ui.back') }}</x-button>
    </div>
</x-layouts.app>
