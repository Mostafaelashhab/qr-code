<x-layouts.app :title="__('ui.new_role')">
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('tenant.roles.store') }}" class="space-y-6">
            @csrf
            @include('tenant.roles._form', ['role' => $role, 'permissions' => $permissions])

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.roles.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
