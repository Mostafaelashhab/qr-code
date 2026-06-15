@php
    $initialOf = fn ($u): string => (string) \Illuminate\Support\Str::of($u->name)->trim()->substr(0, 1)->upper();
    $roleLabel = fn ($u): string => $u->isClientAdmin() ? $u->role->label() : ($u->staffRole?->name ?? $u->role->label());
@endphp

<x-layouts.app :title="__('ui.users')">
    <x-page-header :title="__('ui.users')" :subtitle="number_format($users->total()).' '.__('ui.users')">
        <x-slot:actions>
            <x-button :href="route('tenant.users.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_user') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($users->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :action-label="__('ui.new_user')" :action-href="route('tenant.users.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.name') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.role') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.status') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($users as $user)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($user) }}</span>
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="truncate text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5">
                        <x-badge :color="$user->isClientAdmin() ? 'indigo' : 'gray'">{{ $roleLabel($user) }}</x-badge>
                    </td>
                    <td class="px-6 py-3.5">
                        <x-badge :color="$user->is_active ? 'emerald' : 'gray'">{{ $user->is_active ? __('ui.active') : __('ui.inactive') }}</x-badge>
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('tenant.users.edit', $user) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                            @unless ($user->is(auth()->user()))
                                <form method="POST" action="{{ route('tenant.users.destroy', $user) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                                </form>
                            @endunless
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $users->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($users as $user)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">{{ $initialOf($user) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <x-badge :color="$user->is_active ? 'emerald' : 'gray'">{{ $user->is_active ? __('ui.active') : __('ui.inactive') }}</x-badge>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                        <x-badge :color="$user->isClientAdmin() ? 'indigo' : 'gray'">{{ $roleLabel($user) }}</x-badge>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('tenant.users.edit', $user) }}" class="font-medium text-indigo-600">{{ __('ui.edit') }}</a>
                            @unless ($user->is(auth()->user()))
                                <form method="POST" action="{{ route('tenant.users.destroy', $user) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="font-medium text-rose-600">{{ __('ui.delete') }}</button>
                                </form>
                            @endunless
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $users->links() }}</div>
    @endif
</x-layouts.app>
