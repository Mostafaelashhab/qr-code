<x-layouts.app :title="__('ui.dashboard')">
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ $client->name }}</h2>
    </div>

    @unless ($onboardingDone)
        @php $doneCount = collect($onboarding)->where('done', true)->count(); @endphp
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-indigo-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold tracking-tight">{{ __('ui.getting_started') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('ui.getting_started_hint') }}</p>
                </div>
                <span class="text-sm font-medium text-indigo-600">{{ $doneCount }}/{{ count($onboarding) }}</span>
            </div>

            <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-indigo-500" style="width: {{ (int) round($doneCount / count($onboarding) * 100) }}%"></div>
            </div>

            <ul class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($onboarding as $step)
                    <li>
                        <a href="{{ $step['url'] }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm ring-1 transition {{ $step['done'] ? 'ring-emerald-100 bg-emerald-50/50' : 'ring-gray-200 hover:bg-gray-50' }}">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                @if ($step['done'])
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                @endif
                            </span>
                            <span class="{{ $step['done'] ? 'text-gray-500 line-through' : 'font-medium text-gray-800' }}">{{ $step['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endunless

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card :label="__('ui.students')" :value="$stats['students']" />
        <x-stat-card :label="__('ui.groups')" :value="$stats['groups']" />
        <x-stat-card :label="__('ui.teachers')" :value="$stats['teachers']" />
        <x-stat-card :label="__('ui.this_month_revenue')" :value="number_format((float) $stats['month_revenue'], 2)" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.this_month_revenue')" class="lg:col-span-2">
            <x-bar-chart :data="$revenueByMonth" color="indigo" />
        </x-card>

        <x-card :title="__('ui.attendance_rate')">
            <div class="flex h-44 flex-col items-center justify-center gap-3">
                <div class="relative flex size-32 items-center justify-center rounded-full"
                     style="background: conic-gradient(#10b981 {{ $attendanceRate * 3.6 }}deg, #e5e7eb 0deg)">
                    <div class="flex size-24 items-center justify-center rounded-full bg-white">
                        <span class="text-2xl font-bold tabular-nums">{{ $attendanceRate }}%</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500">{{ __('ui.attendance_rate') }}</p>
            </div>
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card :title="__('ui.students')">
            <x-bar-chart :data="$studentsByMonth" color="emerald" />
        </x-card>

        <x-card :title="__('ui.payments')" class="lg:col-span-2">
            <ul class="divide-y divide-gray-100">
                @forelse ($recentPayments as $payment)
                    <li class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $payment->student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->group?->name ?? '—' }}</p>
                        </div>
                        <div class="text-end">
                            <p class="font-medium">{{ number_format((float) $payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->paid_at->isoFormat('LL') }}</p>
                        </div>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                @endforelse
            </ul>
        </x-card>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">{{ __('ui.current_subscription') }}</p>
                @if ($subscription)
                    <p class="mt-2 text-xl font-semibold">{{ $subscription->plan->name }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge>
                        @if ($subscription->daysRemaining() !== null)
                            <span class="text-xs text-gray-500">{{ $subscription->daysRemaining() }} {{ __('ui.days_remaining') }}</span>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-sm text-gray-500">{{ __('ui.no_active_subscription') }}</p>
                @endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <p class="mb-3 text-sm font-medium text-gray-500">{{ __('ui.plan_usage') }}</p>
                @foreach (['users' => __('ui.users_usage'), 'students' => __('ui.students_usage')] as $key => $label)
                    @php $limit = $usage[$key]['limit']; $used = $usage[$key]['used']; @endphp
                    <div class="mb-3 last:mb-0">
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ $label }}</span>
                            <span class="text-gray-500">{{ $used }} / {{ $limit ?? __('ui.unlimited') }}</span>
                        </div>
                        @if ($limit)
                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, (int) round($used / max(1, $limit) * 100)) }}%"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
