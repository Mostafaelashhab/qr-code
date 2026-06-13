<x-layouts.guest :title="__('ui.reset_password')">
    <h2 class="mb-6 text-center text-lg font-semibold">{{ __('ui.reset_password') }}</h2>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-form.field name="email" :label="__('ui.email')" type="email" :value="$email" required />
        <x-form.field name="password" :label="__('ui.password')" type="password" required autocomplete="new-password" />
        <x-form.field name="password_confirmation" :label="__('ui.confirm_password')" type="password" required autocomplete="new-password" />

        <x-button type="submit" class="w-full">{{ __('ui.reset_password') }}</x-button>
    </form>
</x-layouts.guest>
