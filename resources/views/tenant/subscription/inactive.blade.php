<x-layouts.app :title="__('ui.no_active_subscription')">
    <div class="mx-auto max-w-lg">
        <x-card>
            <div class="flex flex-col items-center gap-4 py-6 text-center">
                <div class="flex size-14 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold">{{ __('ui.no_active_subscription') }}</h2>
                <p class="text-sm text-gray-500">{{ __('ui.subscription_inactive_notice') }}</p>
                @if (auth()->user()->isClientAdmin())
                    <x-button :href="route('tenant.billing.index')">{{ __('ui.subscribe_pay') }}</x-button>
                @endif
                <x-whatsapp-support class="mt-1" :label="__('ui.need_help')" />
            </div>
        </x-card>
    </div>
</x-layouts.app>
