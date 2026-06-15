@php
    $user = auth()->user();

    // Quick-action visibility mirrors the sidebar's permission/plan gating.
    $canStudents = $user->hasPermission(\App\Enums\Permission::Students);
    $canGroups = $user->hasPermission(\App\Enums\Permission::Groups);
    $canPayments = $user->hasPermission(\App\Enums\Permission::Payments)
        && $client->hasFeature(\App\Enums\Feature::Payments);

    $onboardDone = collect($onboarding)->where('done', true)->count();
    $onboardTotal = max(1, count($onboarding));
    $onboardPct = (int) round($onboardDone / $onboardTotal * 100);
@endphp

<x-layouts.app :title="__('ui.dashboard')">
    {{-- Hero header: greeting, center name, and primary quick actions --}}
    <div class="relative mb-6 overflow-hidden rounded-3xl bg-linear-to-br from-indigo-600 via-indigo-600 to-violet-600 p-6 text-white shadow-sm sm:p-8">
        {{-- Decorative glow, hidden from assistive tech --}}
        <div aria-hidden="true" class="pointer-events-none absolute -end-10 -top-10 size-48 rounded-full bg-white/10 blur-2xl"></div>
        <div aria-hidden="true" class="pointer-events-none absolute -bottom-16 end-1/3 size-48 rounded-full bg-violet-400/20 blur-3xl"></div>

        <div class="relative flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-medium text-indigo-100">{{ __('ui.welcome_back') }}, {{ $user->name }}</p>
                <h2 class="mt-1 truncate text-2xl font-bold tracking-tight sm:text-3xl">{{ $client->name }}</h2>
                <p class="mt-1.5 text-sm text-indigo-100/90">{{ __('ui.dashboard_intro') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if ($canStudents)
                    <a href="{{ route('tenant.students.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                        {{ __('ui.new_student') }}
                    </a>
                @endif
                @if ($canPayments)
                    <a href="{{ route('tenant.payments.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/30 backdrop-blur transition hover:bg-white/25">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h20v10H2zM12 9.5a2.5 2.5 0 100 5 2.5 2.5 0 000-5z" /></svg>
                        {{ __('ui.new_payment') }}
                    </a>
                @endif
                @if ($canGroups)
                    <a href="{{ route('tenant.groups.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/30 backdrop-blur transition hover:bg-white/25">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                        {{ __('ui.groups') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Onboarding checklist --}}
    @unless ($onboardingDone)
        <div class="mb-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/70">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 bg-indigo-50/40 px-6 py-5">
                <div class="flex items-center gap-4">
                    {{-- Circular progress --}}
                    <div class="relative flex size-14 shrink-0 items-center justify-center rounded-full"
                         style="background: conic-gradient(#6366f1 {{ $onboardPct * 3.6 }}deg, #e0e7ff 0deg)">
                        <div class="flex size-11 items-center justify-center rounded-full bg-white">
                            <span class="text-xs font-bold tabular-nums text-indigo-700">{{ $onboardPct }}%</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold tracking-tight text-gray-900">{{ __('ui.getting_started') }}</h3>
                        <p class="mt-0.5 text-sm text-gray-500">{{ __('ui.getting_started_hint') }}</p>
                    </div>
                </div>
                <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-indigo-600 ring-1 ring-indigo-100">
                    {{ $onboardDone }}/{{ count($onboarding) }}
                </span>
            </div>

            <ul class="grid grid-cols-1 gap-3 p-6 sm:grid-cols-2">
                @foreach ($onboarding as $i => $step)
                    <li>
                        <a href="{{ $step['url'] }}"
                           class="group flex items-center gap-3 rounded-xl px-4 py-3 ring-1 transition {{ $step['done'] ? 'bg-emerald-50/60 ring-emerald-100' : 'ring-gray-200 hover:border-indigo-200 hover:bg-indigo-50/40 hover:ring-indigo-200' }}">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold {{ $step['done'] ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-700' }}">
                                @if ($step['done'])
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>
                            <span class="flex-1 text-sm {{ $step['done'] ? 'text-gray-500 line-through' : 'font-medium text-gray-800' }}">{{ $step['label'] }}</span>
                            @unless ($step['done'])
                                <svg class="size-4 shrink-0 text-gray-300 transition group-hover:text-indigo-500 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
                            @endunless
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endunless

    {{-- KPI stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <x-stat-card icon="users" color="indigo" :label="__('ui.students')" :value="$stats['students']"
                     :href="$canStudents ? route('tenant.students.index') : null" />
        <x-stat-card icon="group" color="sky" :label="__('ui.groups')" :value="$stats['groups']"
                     :href="$canGroups ? route('tenant.groups.index') : null" />
        <x-stat-card icon="teacher" color="violet" :label="__('ui.teachers')" :value="$stats['teachers']" />
        <x-stat-card icon="cash" color="emerald" :label="__('ui.this_month_revenue')"
                     :value="number_format((float) $stats['month_revenue'], 0)" :hint="$client->currency" />
        <x-stat-card icon="clock" :color="(float) $stats['pending_payments'] > 0 ? 'amber' : 'emerald'"
                     :label="__('ui.pending_payments')"
                     :value="number_format((float) $stats['pending_payments'], 0)" :hint="$client->currency"
                     :href="$canPayments ? route('tenant.payments.index') : null" />
    </div>

    {{-- Revenue trend + attendance rate --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.this_month_revenue')" :subtitle="__('ui.last_6_months')" class="lg:col-span-2">
            <x-bar-chart :data="$revenueByMonth" color="indigo" />
        </x-card>

        <x-card :title="__('ui.attendance_rate')">
            <div class="flex h-48 flex-col items-center justify-center gap-4">
                <div class="relative flex size-36 items-center justify-center rounded-full"
                     style="background: conic-gradient(#10b981 {{ $attendanceRate * 3.6 }}deg, #e5e7eb 0deg)">
                    <div class="flex size-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold tabular-nums text-gray-900">{{ $attendanceRate }}%</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 text-sm text-gray-500">
                    <span class="size-2.5 rounded-full bg-emerald-500"></span>
                    {{ __('ui.attendance') }}
                </span>
            </div>
        </x-card>
    </div>

    {{-- New students + recent payments --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.new_students')" :subtitle="__('ui.last_6_months')">
            <x-bar-chart :data="$studentsByMonth" color="emerald" />
        </x-card>

        <x-card :title="__('ui.recent_payments')" class="lg:col-span-2">
            @if ($canPayments)
                <x-slot:actions>
                    <a href="{{ route('tenant.payments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">{{ __('ui.view_all') }}</a>
                </x-slot:actions>
            @endif

            <ul class="divide-y divide-gray-100">
                @forelse ($recentPayments as $payment)
                    <li class="flex items-center gap-3 py-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-600">
                            {{ \Illuminate\Support\Str::of($payment->student->name)->trim()->substr(0, 1)->upper() }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $payment->student->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $payment->group?->name ?? '—' }}</p>
                        </div>
                        <div class="text-end">
                            <p class="text-sm font-semibold tabular-nums text-emerald-600">+{{ number_format((float) $payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-400">{{ $payment->paid_at->isoFormat('LL') }}</p>
                        </div>
                    </li>
                @empty
                    <li class="py-10">
                        <p class="text-center text-sm text-gray-400">{{ __('ui.no_results') }}</p>
                    </li>
                @endforelse
            </ul>
        </x-card>
    </div>

    {{-- Subscription + plan usage --}}
    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/70">
            <p class="text-sm font-medium text-gray-500">{{ __('ui.current_subscription') }}</p>
            @if ($subscription)
                <p class="mt-2 text-xl font-semibold tracking-tight">{{ $subscription->plan->name }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge>
                    @if ($subscription->daysRemaining() !== null)
                        <span class="text-xs text-gray-500">{{ $subscription->daysRemaining() }} {{ __('ui.days_remaining') }}</span>
                    @endif
                </div>
            @else
                <p class="mt-2 text-sm text-gray-500">{{ __('ui.no_active_subscription') }}</p>
            @endif
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/70">
            <p class="mb-3 text-sm font-medium text-gray-500">{{ __('ui.plan_usage') }}</p>
            @foreach (['users' => __('ui.users_usage'), 'students' => __('ui.students_usage')] as $key => $label)
                @php $limit = $usage[$key]['limit']; $used = $usage[$key]['used']; @endphp
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-700">{{ $label }}</span>
                        <span class="tabular-nums text-gray-500">{{ $used }} / {{ $limit ?? __('ui.unlimited') }}</span>
                    </div>
                    @if ($limit)
                        @php $usePct = min(100, (int) round($used / max(1, $limit) * 100)); @endphp
                        <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full {{ $usePct >= 90 ? 'bg-rose-500' : 'bg-indigo-500' }}" style="width: {{ $usePct }}%"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
