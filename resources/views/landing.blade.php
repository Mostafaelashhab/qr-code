@php
    use App\Enums\Feature;

    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';

    $featureIcons = [
        // students & groups
        'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
        // QR attendance
        'M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2M7 12h10',
        // exams & grades
        'M9 11l3 3 8-8M22 12a10 10 0 11-5.93-9.14',
        // timetable
        'M3 4h18M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v13a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z',
        // balances & installments
        'M2 8h20M2 6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2z',
        // teacher payroll
        'M21 12V7H5a2 2 0 010-4h14v4M3 5v14a2 2 0 002 2h16v-5M18 12a2 2 0 000 4h4v-4z',
        // parent portal (share link)
        'M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71',
        // reports (chart)
        'M3 3v18h18M7 16v-5M12 16V8M17 16v-9',
    ];

    $heroPoints = [__('landing.hero_point_1'), __('landing.hero_point_2'), __('landing.hero_point_3')];
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full scroll-smooth">
<head>
    <x-head-meta :description="__('landing.hero_subtitle')" />
    <x-assets />
</head>
<body class="min-h-full bg-white text-gray-900 antialiased">
    {{-- Header --}}
    <header class="sticky top-0 z-30 border-b border-gray-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex size-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
                    <x-app-logo class="size-5" />
                </span>
                <span class="font-semibold tracking-tight">{{ __('ui.app_name') }}</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="#pricing" class="hidden text-sm font-medium text-gray-600 hover:text-indigo-600 sm:block">{{ __('landing.pricing_title') }}</a>
                <a href="#faq" class="hidden text-sm font-medium text-gray-600 hover:text-indigo-600 sm:block">{{ __('landing.faq_title') }}</a>
                <x-language-switcher class="hidden sm:inline-flex" />
                <x-button :href="route('login')" variant="secondary">{{ __('landing.login') }}</x-button>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-linear-to-b from-indigo-50/70 to-white"></div>
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24">
            <div>
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">
                    {{ __('landing.hero_badge') }}
                </span>
                <h1 class="mt-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{ __('landing.hero_title') }}</h1>
                <p class="mt-5 max-w-xl text-lg text-gray-600">{{ __('landing.hero_subtitle') }}</p>

                <ul class="mt-6 space-y-2.5">
                    @foreach ($heroPoints as $point)
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <span class="flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            </span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <x-button :href="route('register')">{{ __('landing.get_started') }}</x-button>
                    <x-button :href="route('login')" variant="secondary">{{ __('landing.login') }}</x-button>
                </div>
            </div>

            {{-- Dashboard preview mockup --}}
            <div class="relative">
                <div class="absolute -inset-4 -z-10 rounded-3xl bg-linear-to-tr from-indigo-200/40 to-transparent blur-2xl"></div>
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-200/70">
                    <div class="flex items-center gap-1.5 border-b border-gray-100 px-4 py-3">
                        <span class="size-2.5 rounded-full bg-rose-400"></span>
                        <span class="size-2.5 rounded-full bg-amber-400"></span>
                        <span class="size-2.5 rounded-full bg-emerald-400"></span>
                        <span class="mx-auto text-xs font-medium text-gray-400">{{ __('landing.preview_title') }}</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ([['label' => __('ui.students'), 'value' => '128'], ['label' => __('ui.groups'), 'value' => '14'], ['label' => __('ui.this_month_revenue'), 'value' => '38,400']] as $tile)
                                <div class="rounded-xl bg-gray-50 p-3 ring-1 ring-gray-100">
                                    <p class="truncate text-[11px] text-gray-500">{{ $tile['label'] }}</p>
                                    <p class="mt-1 text-lg font-bold tracking-tight tabular-nums">{{ $tile['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="rounded-xl ring-1 ring-gray-100">
                            @foreach (['Math — Group A', 'Physics — Group B', 'Chemistry — Group A'] as $i => $row)
                                <div class="flex items-center justify-between px-4 py-2.5 text-sm {{ $i > 0 ? 'border-t border-gray-100' : '' }}">
                                    <span class="font-medium text-gray-700">{{ $row }}</span>
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">{{ __('attendance.status.present') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('landing.how_title') }}</h2>
            <p class="mt-3 text-gray-600">{{ __('landing.how_subtitle') }}</p>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach (__('landing.steps') as $i => $step)
                <div class="relative rounded-2xl bg-white p-6 ring-1 ring-gray-200/70">
                    <span class="flex size-10 items-center justify-center rounded-full bg-indigo-600 text-base font-bold text-white">{{ $i + 1 }}</span>
                    <h3 class="mt-4 font-semibold">{{ $step['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-gray-600">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-gray-50 py-16 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight">{{ __('landing.features_title') }}</h2>
                <p class="mt-3 text-gray-600">{{ __('landing.features_subtitle') }}</p>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (__('landing.features') as $i => $feature)
                    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-200/70 transition hover:shadow-md">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $featureIcons[$i] ?? $featureIcons[0] }}" />
                            </svg>
                        </span>
                        <h3 class="mt-4 font-semibold">{{ $feature['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('landing.testimonials_title') }}</h2>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach (__('landing.testimonials') as $t)
                <figure class="flex flex-col rounded-2xl bg-white p-6 ring-1 ring-gray-200/70">
                    <div class="mb-3 flex gap-0.5 text-amber-400">
                        @for ($i = 0; $i < 5; $i++)<span>★</span>@endfor
                    </div>
                    <blockquote class="flex-1 text-sm leading-relaxed text-gray-700">“{{ $t['quote'] }}”</blockquote>
                    <figcaption class="mt-4 flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">{{ \Illuminate\Support\Str::substr($t['name'], 0, 1) }}</span>
                        <span>
                            <span class="block text-sm font-semibold">{{ $t['name'] }}</span>
                            <span class="block text-xs text-gray-500">{{ $t['role'] }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-24">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('landing.pricing_title') }}</h2>
            <p class="mt-3 text-gray-600">{{ __('landing.pricing_subtitle') }}</p>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($plans as $plan)
                @php $featured = $loop->index === 1; @endphp
                <div class="relative flex flex-col rounded-2xl bg-white p-7 shadow-sm ring-1 {{ $featured ? 'ring-2 ring-indigo-500' : 'ring-gray-200/70' }}">
                    @if ($featured)
                        <span class="absolute -top-3 end-6 rounded-full bg-indigo-600 px-3 py-1 text-xs font-semibold text-white shadow-sm">{{ __('landing.popular') }}</span>
                    @endif
                    <h3 class="font-semibold tracking-tight">{{ $plan->name }}</h3>
                    <p class="mt-4 flex items-baseline gap-1.5">
                        <span class="text-4xl font-bold tracking-tight tabular-nums">{{ number_format((float) $plan->price, 0) }}</span>
                        <span class="text-sm text-gray-500">/ {{ $plan->billing_period->label() }}</span>
                    </p>

                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span>{{ $plan->max_users ?? __('landing.unlimited') }} {{ __('landing.users') }}</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span>{{ $plan->max_students ?? __('landing.unlimited') }} {{ __('landing.students') }}</li>
                        @foreach (($plan->features ?? []) as $featureKey)
                            @php $feature = Feature::tryFrom($featureKey); @endphp
                            @if ($feature)
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span>{{ $feature->label() }}</li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="mt-auto pt-7">
                        <x-button :href="route('register')" :variant="$featured ? 'primary' : 'secondary'" class="w-full">
                            {{ __('landing.choose_plan') }}
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="bg-gray-50 py-16 lg:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight">{{ __('landing.faq_title') }}</h2>
            </div>
            <div class="mt-10 space-y-3">
                @foreach (__('landing.faqs') as $faq)
                    <details class="group rounded-2xl bg-white p-5 ring-1 ring-gray-200/70">
                        <summary class="flex cursor-pointer items-center justify-between gap-4 font-medium text-gray-900 marker:content-['']">
                            {{ $faq['q'] }}
                            <span class="text-gray-400 transition group-open:rotate-45">+</span>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-24">
        <div class="rounded-3xl bg-indigo-600 px-6 py-14 text-center text-white sm:px-12">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('landing.cta_title') }}</h2>
            <p class="mx-auto mt-3 max-w-xl text-indigo-100">{{ __('landing.cta_subtitle') }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="inline-flex h-11 items-center rounded-lg bg-white px-6 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                    {{ __('landing.get_started') }}
                </a>
                <a href="{{ route('login') }}" class="inline-flex h-11 items-center rounded-lg px-6 text-sm font-semibold text-white ring-1 ring-white/40 transition hover:bg-white/10">
                    {{ __('landing.login') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 py-8 sm:flex-row sm:px-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <x-app-logo class="size-5 text-indigo-600" />
                <span>© {{ now()->year }} {{ __('ui.app_name') }}. {{ __('landing.rights') }}</span>
            </div>
            <div class="flex items-center gap-5">
                <x-whatsapp-support :label="__('ui.support')" />
                <x-language-switcher />
            </div>
        </div>
    </footer>

    <x-whatsapp-support floating />
</body>
</html>
