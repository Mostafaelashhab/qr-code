<x-layouts.app :title="__('ui.edit_role')">
    <div class="mx-auto max-w-3xl">
        <x-page-header :title="__('ui.edit_role')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.roles'), 'url' => route('tenant.roles.index')],
            ['label' => $role->name],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('tenant.roles._form', ['role' => $role, 'permissions' => $permissions])

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.roles.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
