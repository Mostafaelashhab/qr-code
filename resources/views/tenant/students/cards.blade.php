<x-layouts.print :title="__('ui.qr_cards')">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        @forelse ($students as $student)
            <div class="flex flex-col items-center gap-2 rounded-xl p-4 text-center ring-1 ring-gray-200">
                <canvas class="qr" data-token="{{ $student->qr_token }}" width="128" height="128"></canvas>
                <p class="text-sm font-semibold leading-tight">{{ $student->name }}</p>
                <p class="text-[11px] text-gray-500">{{ $student->stage }}</p>
            </div>
        @empty
            <p class="col-span-full py-8 text-center text-sm text-gray-500">{{ __('ui.no_results') }}</p>
        @endforelse
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        document.querySelectorAll('canvas.qr').forEach(function (el) {
            new QRious({ element: el, value: el.dataset.token, size: 128, level: 'M' });
        });
    </script>
</x-layouts.print>
