<x-layouts.app :title="__('ui.roles')">
    <x-page-header :title="__('ui.roles')" :subtitle="number_format($roles->total()).' '.__('ui.roles')">
        <x-slot:actions>
            <x-button :href="route('tenant.roles.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_role') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($roles->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.no_roles')" :action-label="__('ui.new_role')" :action-href="route('tenant.roles.create')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.role_name') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.permissions') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.staff_count') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($roles as $role)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="font-medium text-gray-900">{{ $role->name }}</div>
                        @if ($role->is_default)
                            <div class="text-xs text-gray-500">{{ __('ui.default_role') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($role->permissions() as $permission)
                                <x-badge color="indigo">{{ $permission->label() }}</x-badge>
                            @empty
                                <span class="text-xs text-gray-400">{{ __('ui.no_permissions') }}</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $role->users_count }}</span>
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('tenant.roles.edit', $role) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                            @if ($role->users_count === 0)
                                <form method="POST" action="{{ route('tenant.roles.destroy', $role) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $roles->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($roles as $role)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">{{ $role->name }}</p>
                            @if ($role->is_default)
                                <p class="text-xs text-gray-500">{{ __('ui.default_role') }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 tabular-nums">{{ $role->users_count }} {{ __('ui.staff_count') }}</span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @forelse ($role->permissions() as $permission)
                            <x-badge color="indigo">{{ $permission->label() }}</x-badge>
                        @empty
                            <span class="text-xs text-gray-400">{{ __('ui.no_permissions') }}</span>
                        @endforelse
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-4 border-t border-gray-100 pt-3 text-xs">
                        <a href="{{ route('tenant.roles.edit', $role) }}" class="font-medium text-indigo-600">{{ __('ui.edit') }}</a>
                        @if ($role->users_count === 0)
                            <form method="POST" action="{{ route('tenant.roles.destroy', $role) }}"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-medium text-rose-600">{{ __('ui.delete') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $roles->links() }}</div>
    @endif
</x-layouts.app>
