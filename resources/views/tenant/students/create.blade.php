<x-layouts.app :title="__('ui.new_student')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.new_student')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.students'), 'url' => route('tenant.students.index')],
            ['label' => __('ui.new_student')],
        ]" />

        <form method="POST" action="{{ route('tenant.students.store') }}" class="space-y-6">
            @csrf
            @include('tenant.students._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.students.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
