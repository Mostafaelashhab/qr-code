<x-layouts.app :title="__('ui.edit_group')">
    <div class="mx-auto max-w-2xl space-y-6">
        <x-page-header :title="__('ui.edit_group')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
            ['label' => __('ui.groups'), 'url' => route('tenant.groups.index')],
            ['label' => $group->name, 'url' => route('tenant.groups.show', $group)],
            ['label' => __('ui.edit')],
        ]" />

        <form method="POST" action="{{ route('tenant.groups.update', $group) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.groups._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.groups.show', $group)">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>

        {{-- Danger zone: irreversible delete, set apart from the save flow --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-rose-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-rose-700">{{ __('ui.delete_group') }}</h3>
                    <p class="mt-0.5 text-xs text-gray-500">{{ __('ui.delete_group_hint') }}</p>
                </div>
                <form method="POST" action="{{ route('tenant.groups.destroy', $group) }}"
                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger">{{ __('ui.delete') }}</x-button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
