<x-layouts.app :title="__('whatsapp.title')">
    <div class="mx-auto max-w-2xl space-y-6">
        <x-card :title="__('whatsapp.title')">
            <p class="mb-5 text-sm text-gray-600">{{ __('whatsapp.intro') }}</p>

            <div class="mb-5 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 ring-1 ring-gray-200">
                <span class="text-sm font-medium text-gray-700">{{ __('whatsapp.connection_status') }}</span>
                <span id="wa-status">
                    <x-badge :color="$session->isConnected() ? 'emerald' : 'gray'">{{ $session->status->label() }}</x-badge>
                </span>
            </div>

            {{-- (1) Not provisioned yet by the platform --}}
            <div id="wa-preparing" class="@if ($session->isProvisioned()) hidden @endif rounded-lg bg-amber-50 px-4 py-6 text-center text-sm text-amber-800 ring-1 ring-amber-200">
                <p class="font-medium">{{ __('whatsapp.preparing_title') }}</p>
                <p class="mt-1 text-xs text-amber-700">{{ __('whatsapp.preparing_hint') }}</p>
            </div>

            {{-- (2) Connected --}}
            <div id="wa-connected" class="@if (! $session->isConnected()) hidden @endif rounded-lg bg-emerald-50 px-4 py-4 text-sm text-emerald-800 ring-1 ring-emerald-200">
                <p class="font-medium">{{ __('whatsapp.connected_title') }}</p>
                <p class="mt-1 text-xs text-emerald-700">{{ __('whatsapp.connected_hint') }}</p>
            </div>

            {{-- (3) Provisioned, waiting to link --}}
            <div id="wa-link" class="@if (! $session->isProvisioned() || $session->isConnected()) hidden @endif">
                <div class="flex flex-col items-center gap-4 rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center">
                    <img id="wa-qr" src="" alt="WhatsApp QR" class="hidden size-64 rounded-lg ring-1 ring-gray-200">
                    <p id="wa-qr-hint" class="text-sm text-gray-500">{{ __('whatsapp.waiting_hint') }}</p>
                </div>

                <ol class="mt-6 list-decimal space-y-1 ps-5 text-xs text-gray-500">
                    <li>{{ __('whatsapp.step_1') }}</li>
                    <li>{{ __('whatsapp.step_2') }}</li>
                    <li>{{ __('whatsapp.step_3') }}</li>
                </ol>
            </div>
        </x-card>
    </div>

    @push('scripts')
        <script>
            (function () {
                const qrUrl = @json(route('tenant.whatsapp.qr'));
                const els = {
                    status: document.getElementById('wa-status'),
                    preparing: document.getElementById('wa-preparing'),
                    connected: document.getElementById('wa-connected'),
                    link: document.getElementById('wa-link'),
                    qr: document.getElementById('wa-qr'),
                    qrHint: document.getElementById('wa-qr-hint'),
                };
                const labels = { scan: @json(__('whatsapp.scan_hint')), waiting: @json(__('whatsapp.waiting_hint')) };
                let wasConnected = @json($session->isConnected());

                function badge(connected, label) {
                    els.status.innerHTML = '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '
                        + (connected ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700') + '">' + label + '</span>';
                }

                async function poll() {
                    try {
                        const res = await fetch(qrUrl, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();

                        if (data.status_label) badge(data.connected, data.status_label);

                        // Just linked → refresh so the server view settles.
                        if (data.connected && !wasConnected) { window.location.reload(); return; }
                        wasConnected = data.connected;

                        els.preparing.classList.toggle('hidden', data.provisioned !== false);
                        els.connected.classList.toggle('hidden', !data.connected);
                        els.link.classList.toggle('hidden', !(data.provisioned && !data.connected));

                        if (data.provisioned && !data.connected) {
                            if (data.qr) {
                                els.qr.src = data.qr;
                                els.qr.classList.remove('hidden');
                                els.qrHint.textContent = labels.scan;
                            } else {
                                els.qr.classList.add('hidden');
                                els.qrHint.textContent = labels.waiting;
                            }
                        }
                    } catch (e) { /* transient — keep polling */ }
                }

                poll();
                setInterval(poll, 4000);
            })();
        </script>
    @endpush
</x-layouts.app>
