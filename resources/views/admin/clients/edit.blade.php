<x-layouts.app :title="__('ui.edit_client')">
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card :title="__('ui.client_info')">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form.field name="name" :label="__('ui.name')" :value="$client->name" required />
                    </div>
                    <x-form.field name="email" :label="__('ui.email')" type="email" :value="$client->email" />
                    <x-form.field name="phone" :label="__('ui.phone')" :value="$client->phone" />
                    <div class="sm:col-span-2">
                        <x-form.field name="address" :label="__('ui.address')" :value="$client->address" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$client->is_active" />
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.clients.show', $client)">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
