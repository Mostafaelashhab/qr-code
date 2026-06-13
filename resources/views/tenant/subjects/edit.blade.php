<x-layouts.app :title="__('ui.edit_subject')">
    <div class="mx-auto max-w-xl">
        <form method="POST" action="{{ route('tenant.subjects.update', $subject) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.subjects._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.subjects.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
