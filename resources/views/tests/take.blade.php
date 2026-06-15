@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('app.rtl_locales', [])) ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full">
<head>
    <x-head-meta :title="$test->title" :noindex="true" />
    <x-assets />
</head>
<body class="min-h-full bg-gray-50 text-gray-900 antialiased">
    {{-- Sticky timer --}}
    <div class="sticky top-0 z-20 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-2xl items-center justify-between px-4 py-3">
            <span class="truncate font-semibold tracking-tight">{{ $test->title }}</span>
            <span class="flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-700">
                {{ __('ui.time_left') }}: <span id="timer" class="tabular-nums">--:--</span>
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('test.submit', [$test->token, $attempt->id]) }}" id="test-form" class="mx-auto max-w-2xl space-y-5 px-4 py-8">
        @csrf
        @foreach ($questions as $i => $question)
            <x-card>
                <p class="font-medium">{{ $i + 1 }}. {{ $question->body }}
                    <span class="text-xs text-gray-400">({{ $question->points }} {{ __('ui.points') }})</span>
                </p>
                <div class="mt-3 space-y-2">
                    @foreach ($question->options as $option)
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm ring-1 ring-gray-200 transition hover:bg-gray-50 has-checked:bg-indigo-50 has-checked:font-medium has-checked:text-indigo-800 has-checked:ring-indigo-300">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="size-4 text-indigo-600 focus:ring-indigo-600">
                            <span>{{ $option->body }}</span>
                        </label>
                    @endforeach
                </div>
            </x-card>
        @endforeach

        <x-button type="submit" class="w-full">{{ __('ui.submit_test') }}</x-button>
    </form>

    <script>
        (function () {
            let left = {{ (int) $secondsLeft }};
            const el = document.getElementById('timer');
            const form = document.getElementById('test-form');
            let submitted = false;

            function fmt(s) {
                const m = Math.floor(s / 60), r = s % 60;
                return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
            }
            function tick() {
                el.textContent = fmt(Math.max(0, left));
                if (left <= 0 && !submitted) { submitted = true; form.submit(); return; }
                left--; setTimeout(tick, 1000);
            }
            tick();
            form.addEventListener('submit', () => { submitted = true; });
        })();
    </script>
</body>
</html>
