@php
    $locales = [
        'en' => 'English',
        'ar' => 'العربية',
    ];
    $current = app()->getLocale();
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-lg bg-gray-100 p-1 text-sm']) }}>
    @foreach ($locales as $code => $label)
        <form method="POST" action="{{ route('locale.update', $code) }}">
            @csrf
            @method('PUT')
            <button type="submit"
                    class="rounded-md px-2.5 py-1 font-medium transition
                        {{ $current === $code ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        </form>
    @endforeach
</div>
