<x-layouts.app :title="__('ui.new_user')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.new_user')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.users'), 'url' => route('tenant.users.index')],
            ['label' => __('ui.new_user')],
        ]" />

        <form method="POST" action="{{ route('tenant.users.store') }}" class="space-y-6">
            @csrf
            @include('tenant.users._form', ['user' => null])

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.users.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
