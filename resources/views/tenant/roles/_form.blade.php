@php
    /** @var \App\Models\Role $role */
    /** @var array<int, \App\Enums\Permission> $permissions */
    $granted = old('permissions', $role->exists
        ? $role->permissions()->map(fn (\App\Enums\Permission $p) => $p->value)->all()
        : []);
@endphp

<x-card>
    <div class="space-y-6">
        <div class="max-w-md">
            <x-form.field name="name" :label="__('ui.role_name')" :value="$role->name" required />
        </div>

        <div>
            <p class="mb-1 text-sm font-medium text-gray-700">{{ __('ui.permissions') }}</p>
            <p class="mb-4 text-xs text-gray-500">{{ __('ui.permissions_hint') }}</p>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($permissions as $permission)
                    <label class="flex items-center gap-2.5 rounded-lg border border-gray-200 px-3.5 py-2.5 hover:bg-gray-50">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->value }}"
                               @checked(in_array($permission->value, $granted, true))
                               class="size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <span class="text-sm text-gray-700">{{ $permission->label() }}</span>
                    </label>
                @endforeach
            </div>

            @error('permissions')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</x-card>
