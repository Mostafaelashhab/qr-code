@props(['title' => null])

@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
    $user = auth()->user();
    $can = fn (\App\Enums\Feature $feature): bool => (bool) $user->client?->hasFeature($feature);
    // A nav section is visible only when the staff member holds the permission
    // (center admins hold all). Plan-feature sections also still require $can().
    $may = fn (\App\Enums\Permission $permission): bool => $user->hasPermission($permission);

    $nav = $user->isSuperAdmin()
        ? [
            ['route' => 'admin.dashboard', 'label' => __('ui.dashboard'), 'icon' => 'grid'],
            ['route' => 'admin.clients.index', 'label' => __('ui.clients'), 'icon' => 'building', 'active' => 'admin.clients.*'],
            ['route' => 'admin.plans.index', 'label' => __('ui.plans'), 'icon' => 'tag', 'active' => 'admin.plans.*'],
            ['route' => 'admin.subscriptions.index', 'label' => __('ui.subscriptions'), 'icon' => 'card', 'active' => 'admin.subscriptions.*'],
            ['route' => 'admin.subscription-payments.index', 'label' => __('ui.subscription_payments'), 'icon' => 'card', 'active' => 'admin.subscription-payments.*'],
            ['route' => 'admin.reports', 'label' => __('ui.platform_reports'), 'icon' => 'grid'],
        ]
        : array_values(array_filter([
            ['route' => 'tenant.dashboard', 'label' => __('ui.dashboard'), 'icon' => 'grid'],
            $may(\App\Enums\Permission::Students)
                ? ['route' => 'tenant.students.index', 'label' => __('ui.students'), 'icon' => 'users', 'active' => 'tenant.students.*']
                : null,
            $may(\App\Enums\Permission::Groups)
                ? ['route' => 'tenant.groups.index', 'label' => __('ui.groups'), 'icon' => 'grid', 'active' => 'tenant.groups.*']
                : null,
            $can(\App\Enums\Feature::Timetable) && $may(\App\Enums\Permission::Timetable)
                ? ['route' => 'tenant.timetable.index', 'label' => __('ui.timetable'), 'icon' => 'card', 'active' => 'tenant.timetable.*']
                : null,
            $may(\App\Enums\Permission::Subjects)
                ? ['route' => 'tenant.subjects.index', 'label' => __('ui.subjects'), 'icon' => 'tag', 'active' => 'tenant.subjects.*']
                : null,
            $may(\App\Enums\Permission::Teachers)
                ? ['route' => 'tenant.teachers.index', 'label' => __('ui.teachers'), 'icon' => 'building', 'active' => 'tenant.teachers.*']
                : null,
            $can(\App\Enums\Feature::Payments) && $may(\App\Enums\Permission::Payments)
                ? ['route' => 'tenant.payments.index', 'label' => __('ui.payments'), 'icon' => 'card', 'active' => 'tenant.payments.*']
                : null,
            $can(\App\Enums\Feature::Expenses) && $may(\App\Enums\Permission::Expenses)
                ? ['route' => 'tenant.expenses.index', 'label' => __('ui.expenses'), 'icon' => 'tag', 'active' => 'tenant.expenses.*']
                : null,
            $can(\App\Enums\Feature::OnlineTests) && $may(\App\Enums\Permission::OnlineTests)
                ? ['route' => 'tenant.tests.index', 'label' => __('ui.online_tests'), 'icon' => 'tag', 'active' => 'tenant.tests.*']
                : null,
            $can(\App\Enums\Feature::Reports) && $may(\App\Enums\Permission::Reports)
                ? ['route' => 'tenant.reports.index', 'label' => __('ui.reports'), 'icon' => 'grid', 'active' => 'tenant.reports.*']
                : null,
            $can(\App\Enums\Feature::Messages) && $may(\App\Enums\Permission::Messages)
                ? ['route' => 'tenant.messages.index', 'label' => __('ui.messages'), 'icon' => 'card', 'active' => 'tenant.messages.*']
                : null,
            $user->isClientAdmin() && $can(\App\Enums\Feature::WhatsApp)
                ? ['route' => 'tenant.whatsapp.show', 'label' => __('whatsapp.nav'), 'icon' => 'card', 'active' => 'tenant.whatsapp.*']
                : null,
            $user->isClientAdmin()
                ? ['route' => 'tenant.users.index', 'label' => __('ui.users'), 'icon' => 'users', 'active' => 'tenant.users.*']
                : null,
            $user->isClientAdmin()
                ? ['route' => 'tenant.roles.index', 'label' => __('ui.roles'), 'icon' => 'tag', 'active' => 'tenant.roles.*']
                : null,
            $user->isClientAdmin() && $can(\App\Enums\Feature::Activity)
                ? ['route' => 'tenant.activity.index', 'label' => __('ui.activity_log'), 'icon' => 'grid', 'active' => 'tenant.activity.*']
                : null,
            $user->isClientAdmin()
                ? ['route' => 'tenant.settings.edit', 'label' => __('ui.settings'), 'icon' => 'tag', 'active' => 'tenant.settings.*']
                : null,
            ['route' => 'tenant.subscription.index', 'label' => __('ui.my_subscription'), 'icon' => 'card', 'active' => 'tenant.subscription.*'],
            $user->isClientAdmin()
                ? ['route' => 'tenant.billing.index', 'label' => __('ui.billing'), 'icon' => 'card', 'active' => 'tenant.billing.*']
                : null,
        ]));

    // Subscription expiry warning (tenant users with an active subscription nearing its end).
    $expiringSubscription = null;
    if (! $user->isSuperAdmin()) {
        $activeSubscription = $user->client?->activeSubscription();
        $daysLeft = $activeSubscription?->daysRemaining();
        if ($daysLeft !== null && $daysLeft <= (int) config('billing.expiry_warning_days', 7)) {
            $expiringSubscription = $activeSubscription;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$title" :noindex="true" />
    <x-assets />
</head>
<body class="h-full bg-gray-50 text-gray-900 antialiased">
<div class="min-h-full lg:flex">
    {{-- Mobile drawer toggle (pure-CSS, works without bundled JS) --}}
    <input type="checkbox" id="nav-toggle" class="peer hidden">

    {{-- Backdrop (mobile only) --}}
    <label for="nav-toggle" class="fixed inset-0 z-30 hidden bg-gray-900/50 backdrop-blur-sm peer-checked:block lg:hidden"></label>

    {{-- Sidebar: off-canvas on mobile, static on desktop --}}
    <aside class="fixed inset-y-0 start-0 z-40 hidden w-64 shrink-0 flex-col border-e border-gray-200 bg-white peer-checked:flex lg:static lg:flex">
        <div class="flex h-16 items-center gap-2.5 border-b border-gray-100 px-5">
            @if ($user->client?->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->client->logo_path) }}" alt="" class="size-8 rounded-lg object-cover ring-1 ring-gray-200">
            @else
                <span class="flex size-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
                    <x-app-logo class="size-5" />
                </span>
            @endif
            <span class="truncate font-semibold tracking-tight">{{ $user->client?->name ?? __('ui.app_name') }}</span>
        </div>
        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-5">
            @foreach ($nav as $item)
                <x-nav-item :route="$item['route']" :icon="$item['icon']" :active="$item['active'] ?? $item['route']">
                    {{ $item['label'] }}
                </x-nav-item>
            @endforeach
        </nav>
        <div class="border-t border-gray-100 px-5 py-3">
            <p class="text-xs text-gray-400">{{ __('ui.app_name') }}</p>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Top bar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-gray-200 bg-white/90 px-4 backdrop-blur lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <label for="nav-toggle" class="inline-flex cursor-pointer rounded-lg p-2 text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 lg:hidden">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M3 6h18M3 12h18M3 18h18" />
                    </svg>
                </label>
                <h1 class="truncate text-lg font-semibold tracking-tight">{{ $title ?? __('ui.dashboard') }}</h1>
            </div>

            @unless ($user->isSuperAdmin())
                <form method="GET" action="{{ route('tenant.search') }}" class="hidden md:block md:w-72">
                    <div class="relative">
                        <svg class="pointer-events-none absolute inset-y-0 start-3 my-auto size-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" /><path d="M21 21l-4.35-4.35" />
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.search_global_placeholder') }}"
                               class="block w-full rounded-lg border-0 bg-gray-50 py-2 ps-9 pe-3 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
                    </div>
                </form>
            @endunless

            <div class="flex items-center gap-3">
                @unless ($user->isSuperAdmin())
                    <details class="group relative">
                        <summary class="flex cursor-pointer items-center rounded-lg p-2 text-gray-500 ring-1 ring-gray-200 hover:bg-gray-50 marker:content-['']">
                            <span class="relative">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                                </svg>
                                @if ($expiringSubscription)
                                    <span class="absolute -end-0.5 -top-0.5 size-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                                @endif
                            </span>
                        </summary>
                        <div class="absolute end-0 z-30 mt-2 w-72 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-200">
                            <p class="border-b border-gray-100 px-4 py-2.5 text-sm font-semibold">{{ __('ui.notifications') }}</p>
                            @if ($expiringSubscription)
                                <a href="{{ route('tenant.billing.index') }}" class="block px-4 py-3 text-sm hover:bg-gray-50">
                                    <span class="font-medium text-amber-700">
                                        {{ $expiringSubscription->daysRemaining() === 0 ? __('ui.subscription_expires_today') : __('ui.subscription_expiring', ['days' => $expiringSubscription->daysRemaining()]) }}
                                    </span>
                                </a>
                            @else
                                <p class="px-4 py-6 text-center text-sm text-gray-500">{{ __('ui.no_notifications') }}</p>
                            @endif
                        </div>
                    </details>
                @endunless
                <x-language-switcher />
                <div class="hidden items-center gap-2.5 sm:flex">
                    <div class="text-end leading-tight">
                        <p class="text-sm font-medium">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->role->label() }}</p>
                    </div>
                    <span class="flex size-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                        {{ \Illuminate\Support\Str::of($user->name)->trim()->substr(0, 1)->upper() }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="{{ __('ui.logout') }}"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 ring-1 ring-gray-200 transition hover:bg-gray-50 hover:text-gray-900">
                        {{ __('ui.logout') }}
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="mx-auto w-full max-w-6xl">
                @if ($expiringSubscription)
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-amber-200">
                        <span class="flex items-center gap-2">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            {{ $expiringSubscription->daysRemaining() === 0 ? __('ui.subscription_expires_today') : __('ui.subscription_expiring', ['days' => $expiringSubscription->daysRemaining()]) }}
                        </span>
                        @if ($user->isClientAdmin())
                            <a href="{{ route('tenant.billing.index') }}" class="font-semibold text-amber-900 underline hover:no-underline">{{ __('ui.renew_now') }}</a>
                        @endif
                    </div>
                @endif

                <x-alert />
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
