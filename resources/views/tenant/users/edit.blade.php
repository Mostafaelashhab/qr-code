<x-layouts.app :title="__('ui.edit_user')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.edit_user')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.users'), 'url' => route('tenant.users.index')],
            ['label' => $user->name],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('tenant.users._form', ['user' => $user])

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.users.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
