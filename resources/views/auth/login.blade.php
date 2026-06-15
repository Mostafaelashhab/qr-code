<x-layouts.guest :title="__('ui.login')">
    <h2 class="mb-6 text-center text-lg font-semibold">{{ __('ui.sign_in_title') }}</h2>

    <x-alert />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-form.field name="email" :label="__('ui.email')" type="email" required autofocus autocomplete="email" />
        <x-form.field name="password" :label="__('ui.password')" type="password" required autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <x-form.checkbox name="remember" :label="__('ui.remember_me')" />
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                {{ __('ui.forgot_password') }}
            </a>
        </div>

        <x-button type="submit" class="w-full">{{ __('ui.login') }}</x-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        {{ __('ui.no_account') }}
        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:underline">{{ __('ui.register') }}</a>
    </p>
</x-layouts.guest>
