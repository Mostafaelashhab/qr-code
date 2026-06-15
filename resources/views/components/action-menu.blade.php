{{-- Compact "⋮" row-action menu for table rows. Wraps x-dropdown; pass x-menu-item
     children (and inline <form>s for destructive actions) as the slot. --}}
<x-dropdown align="end" width="w-44" {{ $attributes }}>
    <x-slot:trigger>
        <span class="inline-flex size-8 items-center justify-center rounded-lg text-gray-500 ring-1 ring-gray-200 transition hover:bg-gray-50 hover:text-gray-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.6" /><circle cx="12" cy="12" r="1.6" /><circle cx="12" cy="19" r="1.6" /></svg>
        </span>
    </x-slot:trigger>
    {{ $slot }}
</x-dropdown>
