@php $canBill = auth()->user()->client?->hasFeature(\App\Enums\Feature::Payments); @endphp

<x-layouts.app :title="$student->name">
    <x-page-header :title="$student->name" :subtitle="$student->stage" :breadcrumbs="[
        ['label' => __('ui.dashboard'), 'url' => route('tenant.dashboard')],
        ['label' => __('ui.students'), 'url' => route('tenant.students.index')],
        ['label' => $student->name],
    ]">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('tenant.students.edit', $student)">{{ __('ui.edit') }}</x-button>
            @if ($canBill)
                <x-button :href="route('tenant.payments.create', ['student_id' => $student->id])">{{ __('ui.new_payment') }}</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex flex-wrap items-center gap-2">
        <x-badge :color="$student->is_active ? 'emerald' : 'gray'">
            {{ $student->is_active ? __('ui.active') : __('ui.inactive') }}
        </x-badge>
        @if ($canBill && $balance > 0)
            <x-badge color="rose">{{ __('ui.balance') }}: {{ number_format($balance, 2) }}</x-badge>
        @elseif ($canBill)
            <x-badge color="emerald">{{ __('ui.paid_up') }}</x-badge>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-card :title="__('ui.student_details')">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.stage') }}</dt><dd>{{ $student->stage ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.phone') }}</dt><dd>{{ $student->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('ui.guardian_phone') }}</dt><dd>{{ $student->guardian_phone ?? '—' }}</dd></div>
                </dl>
            </x-card>

            @if (auth()->user()->client?->hasFeature(\App\Enums\Feature::Attendance))
                <x-card :title="__('ui.qr_code')">
                    <div class="flex flex-col items-center gap-4">
                        <canvas id="student-qr" data-token="{{ $student->qr_token }}" width="160" height="160" class="rounded-lg ring-1 ring-gray-100"></canvas>
                        <x-button variant="secondary" :href="route('tenant.students.card', $student)">{{ __('ui.print_card') }}</x-button>
                    </div>
                </x-card>
            @endif

            <x-card :title="__('ui.parent_portal')">
                <div class="flex flex-col items-center gap-4">
                    <canvas id="portal-qr" data-url="{{ $student->portalUrl() }}" width="140" height="140" class="rounded-lg ring-1 ring-gray-100"></canvas>
                    <p class="text-center text-xs text-gray-500">{{ __('ui.parent_portal_hint') }}</p>
                    <div class="flex w-full gap-2">
                        <input type="text" readonly value="{{ $student->portalUrl() }}" id="portal-url" dir="ltr"
                               class="block w-full rounded-lg border-0 bg-gray-50 px-3 py-2 text-xs ring-1 ring-inset ring-gray-200">
                        <x-button type="button" id="copy-portal" variant="secondary">{{ __('ui.copy') }}</x-button>
                    </div>
                    <div class="flex w-full gap-2">
                        <x-button :href="$student->portalUrl()" variant="secondary" target="_blank" class="flex-1">{{ __('ui.open') }}</x-button>
                        <form method="POST" action="{{ route('tenant.students.portal.regenerate', $student) }}"
                              onsubmit="return confirm('{{ __('ui.regenerate_confirm') }}')">
                            @csrf
                            <x-button type="submit" variant="secondary">{{ __('ui.regenerate') }}</x-button>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <x-card :title="__('ui.groups')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($student->groups as $group)
                        <li class="flex items-center justify-between py-3 text-sm">
                            <a href="{{ route('tenant.groups.show', $group) }}" class="font-medium text-indigo-600 hover:underline">{{ $group->name }}</a>
                            <span class="text-xs text-gray-500">{{ $group->subject->name }}</span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>

            @if ($canBill)
                @php $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all(); @endphp

                {{-- Balance summary --}}
                <div class="grid grid-cols-3 gap-4">
                    <x-stat-card :label="__('ui.total_charged')" :value="number_format($totalCharged, 2)" />
                    <x-stat-card :label="__('ui.total_paid')" :value="number_format($totalPaid, 2)" />
                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/70">
                        <p class="text-sm font-medium text-gray-500">{{ __('ui.balance') }}</p>
                        <p class="mt-1.5 text-3xl font-semibold tracking-tight tabular-nums {{ $balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format(abs($balance), 2) }}
                        </p>
                        @if ($balance < 0)
                            <p class="mt-1 text-xs text-emerald-600">{{ __('ui.credit') }}</p>
                        @endif
                    </div>
                </div>

                <x-card :title="__('ui.charges')">
                    <form method="POST" action="{{ route('tenant.charges.store') }}" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <x-form.field name="title" :label="__('ui.title')" required />
                        <x-form.select name="group_id" :label="__('ui.group')" :options="$groupOptions" :placeholder="__('ui.none')" />
                        <x-form.field name="amount" :label="__('ui.amount')" type="number" step="0.01" min="0" required />
                        <x-form.field name="discount" :label="__('ui.discount')" type="number" step="0.01" min="0" value="0" />
                        <x-form.field name="for_month" :label="__('ui.for_month')" type="month" :value="now()->format('Y-m')" />
                        <div class="flex items-end">
                            <x-button type="submit" class="w-full">{{ __('ui.add_charge') }}</x-button>
                        </div>
                    </form>

                    <ul class="divide-y divide-gray-100">
                        @forelse ($student->charges->sortByDesc('created_at') as $charge)
                            <li class="flex items-center justify-between gap-3 py-3 text-sm">
                                <div>
                                    <p class="font-medium">{{ $charge->title }}
                                        <span class="tabular-nums text-gray-700">· {{ number_format($charge->netAmount(), 2) }}</span>
                                        @if ((float) $charge->discount > 0)
                                            <span class="text-xs text-emerald-600">({{ __('ui.discount') }} {{ number_format((float) $charge->discount, 2) }})</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $charge->group?->name ?? '—' }} · {{ $charge->for_month ?? '' }}</p>
                                </div>
                                <form method="POST" action="{{ route('tenant.charges.destroy', $charge) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                                </form>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                        @endforelse
                    </ul>
                </x-card>

                <x-card :title="__('ui.payments')">
                    <ul class="divide-y divide-gray-100">
                        @forelse ($student->payments->sortByDesc('paid_at') as $payment)
                            <li class="flex items-center justify-between py-3 text-sm">
                                <div>
                                    <p class="font-medium">{{ number_format((float) $payment->amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->group?->name ?? '—' }} · {{ $payment->for_month ?? '' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500">{{ $payment->paid_at->isoFormat('LL') }}</span>
                                    <a href="{{ route('tenant.payments.receipt', $payment) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.print') }}</a>
                                </div>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                        @endforelse
                    </ul>
                </x-card>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <x-button variant="secondary" :href="route('tenant.students.index')">{{ __('ui.back') }}</x-button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        (function () {
            const studentQr = document.getElementById('student-qr');
            if (studentQr && window.QRious) {
                new QRious({ element: studentQr, value: studentQr.dataset.token, size: 160, level: 'M' });
            }
            const portalQr = document.getElementById('portal-qr');
            if (portalQr && window.QRious) {
                new QRious({ element: portalQr, value: portalQr.dataset.url, size: 140, level: 'M' });
            }
            const copyBtn = document.getElementById('copy-portal');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    const input = document.getElementById('portal-url');
                    navigator.clipboard?.writeText(input.value);
                    input.select();
                });
            }
        })();
    </script>
</x-layouts.app>
