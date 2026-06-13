<x-layouts.guest :title="__('ui.forgot_password')">
    <h2 class="mb-2 text-center text-lg font-semibold">{{ __('ui.forgot_password') }}</h2>
    <p class="mb-6 text-center text-sm text-gray-500">{{ __('ui.forgot_password_hint') }}</p>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-form.field name="email" :label="__('ui.email')" type="email" required autofocus />
        <x-button type="submit" class="w-full">{{ __('ui.send_reset_link') }}</x-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:underline">{{ __('ui.back_to_login') }}</a>
    </p>
</x-layouts.guest>
