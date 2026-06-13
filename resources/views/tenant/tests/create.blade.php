<x-layouts.app :title="__('ui.new_test')">
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('tenant.tests.store') }}" class="space-y-6">
            @csrf
            @include('tenant.tests._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.tests.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
