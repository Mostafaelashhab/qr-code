{{-- Standard table shell: consistent card frame, horizontal scroll on small screens,
     styled header. Provide column headers via <x-slot:head> (a set of <th>),
     rows as the default slot (<tr>…), and optional pagination via <x-slot:footer>. --}}
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            @isset($head)
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>{{ $head }}</tr>
                </thead>
            @endisset
            <tbody class="divide-y divide-gray-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 px-6 py-3">{{ $footer }}</div>
    @endisset
</div>
