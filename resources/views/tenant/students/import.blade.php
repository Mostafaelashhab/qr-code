<x-layouts.app :title="__('ui.import_students')">
    <div class="mx-auto max-w-xl">
        <x-card>
            <p class="mb-5 text-sm text-gray-500">{{ __('ui.import_hint') }}</p>

            <div class="mb-5">
                <x-button variant="secondary" :href="route('tenant.students.import.template')">{{ __('ui.download_template') }}</x-button>
            </div>

            <form method="POST" action="{{ route('tenant.students.import.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label for="file" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('ui.choose_file') }}</label>
                    <input id="file" type="file" name="file" accept=".csv,text/csv" required
                           class="block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('file')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-button variant="secondary" :href="route('tenant.students.index')">{{ __('ui.cancel') }}</x-button>
                    <x-button type="submit">{{ __('ui.import') }}</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
