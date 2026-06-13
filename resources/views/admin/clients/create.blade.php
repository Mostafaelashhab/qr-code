<x-layouts.app :title="__('ui.new_client')">
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-6">
            @csrf

            <x-card :title="__('ui.client_info')">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form.field name="name" :label="__('ui.name')" required />
                    </div>
                    <x-form.field name="email" :label="__('ui.email')" type="email" />
                    <x-form.field name="phone" :label="__('ui.phone')" />
                    <div class="sm:col-span-2">
                        <x-form.field name="address" :label="__('ui.address')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="true" />
                    </div>
                </div>
            </x-card>

            <x-card :title="__('ui.owner_account')">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-form.field name="owner_name" :label="__('ui.owner_name')" required />
                    <x-form.field name="owner_email" :label="__('ui.owner_email')" type="email" required />
                    <x-form.field name="owner_phone" :label="__('ui.owner_phone')" />
                    <div></div>
                    <x-form.field name="owner_password" :label="__('ui.password')" type="password" required />
                    <x-form.field name="owner_password_confirmation" :label="__('ui.confirm_password')" type="password" required />
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.clients.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
