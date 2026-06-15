<x-layouts.app :title="__('ui.new_subject')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.new_subject')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.subjects'), 'url' => route('tenant.subjects.index')],
            ['label' => __('ui.new_subject')],
        ]" />

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
