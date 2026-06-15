<x-layouts.app :title="__('ui.edit_student')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.edit_student')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.students'), 'url' => route('tenant.students.index')],
            ['label' => $student->name, 'url' => route('tenant.students.show', $student)],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.students.update', $student) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.students._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.students.show', $student)">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
