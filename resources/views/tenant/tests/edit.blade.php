<x-layouts.app :title="__('ui.edit_test')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.edit_test')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.online_tests'), 'url' => route('tenant.tests.index')],
            ['label' => $test->title, 'url' => route('tenant.tests.show', $test)],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.tests.update', $test) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.tests._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.tests.show', $test)">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
