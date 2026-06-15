<x-layouts.app :title="__('ui.edit_plan')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.edit_plan')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('ui.plans'), 'url' => route('admin.plans.index')],
            ['label' => $plan->name],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.plans._form')

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.plans.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
