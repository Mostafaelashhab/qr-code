@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
    $studentOptions = $students->mapWithKeys(fn ($s) => [$s->id => $s->name])->all();
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$test->title" :noindex="true" />
    <x-assets />
</head>
<body class="min-h-full bg-gray-50 text-gray-900 antialiased">
    <div class="mx-auto flex min-h-full max-w-lg items-center px-4 py-10">
        <div class="w-full">
            <div class="mb-6 text-center">
                <p class="text-sm text-gray-500">{{ $test->group->client->name ?? '' }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight">{{ $test->title }}</h1>
                <p class="mt-2 text-sm text-gray-500">{{ $test->group->name }} · {{ $test->duration_minutes }} {{ __('ui.duration_minutes') }} · {{ $test->questions()->count() }} {{ __('ui.questions') }}</p>
            </div>

            <x-card>
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('test.start', $test->token) }}" class="space-y-5">
                    @csrf
                    <x-form.select name="student_id" :label="__('ui.select_your_name')" :options="$studentOptions" :placeholder="'—'" required />
                    <x-button type="submit" class="w-full">{{ __('ui.take_test') }}</x-button>
                </form>
            </x-card>
        </div>
    </div>
</body>
</html>
