@props(['name', 'label', 'checked' => false])

<label class="flex items-center gap-2.5">
    <input type="hidden" name="{{ $name }}" value="0">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="1"
        @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600']) }}>
    <span class="text-sm text-gray-700">{{ $label }}</span>
</label>
