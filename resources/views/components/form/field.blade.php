@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'hint' => null,
])

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @unless ($required)
            <span class="text-xs font-normal text-gray-400">({{ __('ui.optional') }})</span>
        @endunless
    </label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-0 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600'
            . ($errors->has($name) ? ' ring-rose-400' : ' ring-gray-300')]) }}>

    @if ($hint)
        <p class="text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
