<x-layouts.app :title="__('ui.new_subject')">
    <div class="mx-auto max-w-xl">
        <form method="POST" action="{{ route('tenant.subjects.store') }}" class="space-y-6">
            @csrf
            @include('tenant.subjects._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.subjects.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
