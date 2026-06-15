<x-layouts.app :title="__('ui.new_teacher')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.new_teacher')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.teachers'), 'url' => route('tenant.teachers.index')],
            ['label' => __('ui.new_teacher')],
        ]" />

        <form method="POST" action="{{ route('tenant.teachers.store') }}" class="space-y-6">
            @csrf
            @include('tenant.teachers._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.teachers.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
