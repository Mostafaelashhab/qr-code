@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'required' => false,
    'placeholder' => null,
])

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-0 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600'
            . ($errors->has($name) ? ' ring-rose-400' : ' ring-gray-300')]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $selected) === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
