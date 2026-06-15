@props(['items' => []])  {{-- [['label' => .., 'url' => .., 'active' => bool], ...] --}}

{{-- Segmented tab navigation (links, SSR-friendly — no JS). Use for ?tab= style
     section switching or to jump between related pages. --}}
<nav class="flex gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1" aria-label="Tabs">
    @foreach ($items as $tab)
        @php $active = $tab['active'] ?? false; @endphp
        <a href="{{ $tab['url'] }}" @if ($active) aria-current="page" @endif
           class="whitespace-nowrap rounded-lg px-3.5 py-1.5 text-sm font-medium transition {{ $active ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
