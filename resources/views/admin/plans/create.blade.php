<x-layouts.app :title="__('ui.new_plan')">
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-6">
            @csrf
            @include('admin.plans._form')

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.plans.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
