<x-layouts.guest :title="__('ui.register')">
    <h2 class="mb-1 text-center text-lg font-semibold">{{ __('ui.register_title') }}</h2>
    <p class="mb-6 text-center text-sm text-gray-500">{{ __('ui.register_hint') }}</p>

    <x-alert />

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-form.field name="center_name" :label="__('ui.center_name')" required autofocus />
        <x-form.field name="name" :label="__('ui.name')" required />
        <x-form.field name="email" :label="__('ui.email')" type="email" required autocomplete="email" />
        <x-form.field name="phone" :label="__('ui.phone')" />
        <x-form.field name="password" :label="__('ui.password')" type="password" required autocomplete="new-password" />
        <x-form.field name="password_confirmation" :label="__('ui.confirm_password')" type="password" required autocomplete="new-password" />

        <x-button type="submit" class="w-full">{{ __('ui.register') }}</x-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        {{ __('ui.have_account') }}
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:underline">{{ __('ui.login') }}</a>
    </p>
</x-layouts.guest>
