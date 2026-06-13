<x-layouts.app :title="__('ui.new_group')">
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('tenant.groups.store') }}" class="space-y-6">
            @csrf
            @include('tenant.groups._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.groups.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
