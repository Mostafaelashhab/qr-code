<x-layouts.app :title="__('ui.edit_group')">
    <div class="mx-auto max-w-2xl space-y-4">
        <form method="POST" action="{{ route('tenant.groups.update', $group) }}" class="space-y-6">
            @csrf @method('PUT')
            @include('tenant.groups._form')
            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.groups.show', $group)">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('tenant.groups.destroy', $group) }}"
              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
            @csrf @method('DELETE')
            <x-button type="submit" variant="danger">{{ __('ui.delete') }}</x-button>
        </form>
    </div>
</x-layouts.app>
