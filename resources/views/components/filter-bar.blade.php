{{-- Standard filter/search bar: a GET form styled as a white card. Place labelled
     controls (selects, month/date inputs) and a submit button inside as the slot.
     Used by reports, activity log, and any list that filters by query string. --}}
<form method="GET" {{ $attributes->merge(['class' => 'mb-6 flex flex-wrap items-end gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70']) }}>
    {{ $slot }}
</form>
