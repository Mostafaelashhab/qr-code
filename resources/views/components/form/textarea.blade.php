@props(['name', 'label', 'value' => null, 'rows' => 3])

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        <span class="text-xs font-normal text-gray-400">({{ __('ui.optional') }})</span>
    </label>

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-0 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600'
            . ($errors->has($name) ? ' ring-rose-400' : ' ring-gray-300')]) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
