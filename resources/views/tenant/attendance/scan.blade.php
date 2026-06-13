<x-layouts.app :title="__('ui.qr_attendance')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ $group->name }}</h2>
            <p class="text-sm text-gray-500">{{ $group->subject->name }} · {{ now()->isoFormat('LL') }}</p>
        </div>
        <div class="flex gap-2">
            <x-button variant="secondary" :href="route('tenant.attendance.cards')">{{ __('ui.qr_cards') }}</x-button>
            <x-button variant="secondary" :href="route('tenant.groups.attendance.create', $group)">{{ __('ui.take_attendance') }}</x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card :title="__('ui.qr_checkin')">
            <p class="mb-4 text-sm text-gray-500">{{ __('ui.scan_hint') }}</p>
            <div id="reader" class="overflow-hidden rounded-xl bg-gray-900/5 ring-1 ring-gray-200"></div>

            <div class="mt-4">
                <label for="manual" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('ui.manual_entry') }}</label>
                <div class="flex gap-2">
                    <input id="manual" type="text" class="block w-full rounded-lg border-0 px-3.5 py-2.5 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    <x-button type="button" id="manual-submit">{{ __('ui.create') }}</x-button>
                </div>
            </div>
        </x-card>

        <x-card :title="__('ui.scanned')">
            <ul id="results" class="divide-y divide-gray-100 text-sm">
                <li class="py-3 text-gray-400" id="results-empty">{{ __('ui.no_results') }}</li>
            </ul>
        </x-card>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (function () {
            const endpoint = @json(route('tenant.groups.attendance.scan.store', $group));
            const csrf = @json(csrf_token());
            const results = document.getElementById('results');
            const empty = document.getElementById('results-empty');
            let lastToken = null, lastAt = 0;

            function addResult(ok, text) {
                if (empty) { empty.remove(); }
                const li = document.createElement('li');
                li.className = 'flex items-center justify-between gap-3 py-3';
                li.innerHTML = '<span class="font-medium ' + (ok ? 'text-emerald-700' : 'text-rose-600') + '">' + text + '</span>' +
                    '<span class="text-xs text-gray-400">' + new Date().toLocaleTimeString() + '</span>';
                results.prepend(li);
            }

            function submit(token) {
                if (!token) return;
                const now = Date.now();
                if (token === lastToken && now - lastAt < 3000) return;
                lastToken = token; lastAt = now;

                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ token: token }),
                })
                .then(r => r.json())
                .then(d => addResult(!!d.ok, d.message || (d.name || '')))
                .catch(() => addResult(false, 'Error'));
            }

            document.getElementById('manual-submit').addEventListener('click', function () {
                const el = document.getElementById('manual');
                submit(el.value.trim());
                el.value = '';
            });

            if (window.Html5Qrcode) {
                const scanner = new Html5Qrcode('reader');
                scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 240 }, submit)
                    .catch(() => { document.getElementById('reader').innerHTML =
                        '<p class="p-6 text-center text-sm text-gray-500">' + @json(__('ui.camera_unavailable')) + '</p>'; });
            }
        })();
    </script>
</x-layouts.app>
