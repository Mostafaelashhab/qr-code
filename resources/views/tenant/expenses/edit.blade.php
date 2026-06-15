<x-layouts.app :title="__('ui.edit_expense')">
    <div class="mx-auto max-w-2xl">
        <x-page-header :title="__('ui.edit_expense')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.expenses'), 'url' => route('tenant.expenses.index')],
            ['label' => $expense->title],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.expenses.update', $expense) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.expenses._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.expenses.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
