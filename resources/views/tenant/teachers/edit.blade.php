<x-layouts.app :title="__('ui.edit_teacher')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.edit_teacher')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.teachers'), 'url' => route('tenant.teachers.index')],
            ['label' => $teacher->name],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.teachers.update', $teacher) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.teachers._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.teachers.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
