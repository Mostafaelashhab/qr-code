<x-layouts.app :title="__('ui.roles')">
    <div class="mb-6 flex items-center justify-end">
        <x-button :href="route('tenant.roles.create')">{{ __('ui.new_role') }}</x-button>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.role_name') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.permissions') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.staff_count') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($roles as $role)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $role->name }}</div>
                                @if ($role->is_default)
                                    <div class="text-xs text-gray-500">{{ __('ui.default_role') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($role->permissions() as $permission)
                                        <x-badge color="indigo">{{ $permission->label() }}</x-badge>
                                    @empty
                                        <span class="text-xs text-gray-400">{{ __('ui.no_permissions') }}</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $role->users_count }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('tenant.roles.edit', $role) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                                    @if ($role->users_count === 0)
                                        <form method="POST" action="{{ route('tenant.roles.destroy', $role) }}"
                                              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_roles') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $roles->links() }}</div>
</x-layouts.app>
