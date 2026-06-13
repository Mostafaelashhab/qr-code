<x-layouts.app :title="__('ui.settings')">
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('tenant.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <x-card :title="__('ui.logo')">
                <div class="flex items-center gap-4">
                    @if ($client->logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo_path) }}" alt="" class="size-16 rounded-lg object-cover ring-1 ring-gray-200">
                    @else
                        <div class="flex size-16 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                            <x-app-logo class="size-7" />
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="logo" accept="image/*"
                               class="block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('logo')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </x-card>

            <x-card :title="__('ui.contact_info')">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-form.field name="email" :label="__('ui.email')" type="email" :value="$client->email" />
                    <x-form.field name="phone" :label="__('ui.phone')" :value="$client->phone" />
                    <div class="sm:col-span-2">
                        <x-form.field name="address" :label="__('ui.address')" :value="$client->address" />
                    </div>
                </div>
            </x-card>

            <x-card :title="__('ui.preferences')">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <x-form.field name="currency" :label="__('ui.currency')" :value="$client->currency" required />
                    <x-form.field name="timezone" :label="__('ui.timezone')" :value="$client->timezone" required />
                    <x-form.field name="default_monthly_fee" :label="__('ui.default_monthly_fee')" type="number" step="0.01" min="0" :value="$client->default_monthly_fee" />
                </div>
            </x-card>

            <div class="flex items-center justify-end">
                <x-button type="submit">{{ __('ui.save') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
