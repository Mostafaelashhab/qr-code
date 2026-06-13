@php use App\Enums\QuestionType; $url = route('test.show', $test->token); @endphp

<x-layouts.app :title="$test->title">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-semibold">{{ $test->title }}</h2>
            <x-badge :color="$test->is_published ? 'emerald' : 'gray'">{{ $test->is_published ? __('ui.published') : __('ui.draft') }}</x-badge>
        </div>
        <div class="flex gap-2">
            <x-button variant="secondary" :href="route('tenant.tests.edit', $test)">{{ __('ui.edit') }}</x-button>
            <form method="POST" action="{{ route('tenant.tests.publish', $test) }}">
                @csrf
                <x-button type="submit">{{ $test->is_published ? __('ui.unpublish') : __('ui.publish') }}</x-button>
            </form>
        </div>
    </div>

    <p class="mb-6 text-sm text-gray-500">{{ $test->group->name }} · {{ $test->duration_minutes }} {{ __('ui.duration_minutes') }} · {{ $test->questions->count() }} {{ __('ui.questions') }}</p>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            {{-- Add question --}}
            <x-card :title="__('ui.add_question')">
                <form method="POST" action="{{ route('tenant.tests.questions.store', $test) }}" class="space-y-4">
                    @csrf
                    <x-form.textarea name="body" :label="__('ui.question_text')" />
                    <div class="grid grid-cols-2 gap-4">
                        <x-form.select name="type" :label="__('ui.question_type')" :options="['mcq' => __('tests.type.mcq'), 'true_false' => __('tests.type.true_false')]" id="q-type" />
                        <x-form.field name="points" :label="__('ui.points')" type="number" min="1" value="1" required />
                    </div>

                    <div id="mcq-block" class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('ui.option') }}s · {{ __('ui.correct_answer') }}</label>
                        @for ($i = 0; $i < 4; $i++)
                            <div class="flex items-center gap-2">
                                <input type="radio" name="correct" value="{{ $i }}" @checked($i === 0) class="size-4 text-indigo-600 focus:ring-indigo-600">
                                <input type="text" name="options[]" placeholder="{{ __('ui.option') }} {{ $i + 1 }}"
                                       class="block w-full rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                            </div>
                        @endfor
                    </div>

                    <div id="tf-block" hidden class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('ui.correct_answer') }}</label>
                        <div class="flex gap-4 text-sm">
                            <label class="flex items-center gap-2"><input type="radio" name="correct_tf" value="true" checked class="size-4 text-indigo-600">{{ __('tests.true') }}</label>
                            <label class="flex items-center gap-2"><input type="radio" name="correct_tf" value="false" class="size-4 text-indigo-600">{{ __('tests.false') }}</label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-button type="submit">{{ __('ui.add_question') }}</x-button>
                    </div>
                </form>
            </x-card>

            {{-- Questions list --}}
            <x-card :title="__('ui.questions')">
                <ol class="space-y-4">
                    @forelse ($test->questions as $question)
                        <li class="rounded-lg ring-1 ring-gray-100 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <p class="font-medium">{{ $loop->iteration }}. {{ $question->body }}
                                    <span class="text-xs text-gray-400">({{ $question->points }} {{ __('ui.points') }})</span>
                                </p>
                                <form method="POST" action="{{ route('tenant.tests.questions.destroy', [$test, $question]) }}"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">{{ __('ui.delete') }}</button>
                                </form>
                            </div>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach ($question->options as $option)
                                    <li class="flex items-center gap-2 {{ $option->is_correct ? 'text-emerald-700 font-medium' : 'text-gray-600' }}">
                                        @if ($option->is_correct)<span>✓</span>@else<span class="text-gray-300">•</span>@endif
                                        {{ $option->body }}
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">{{ __('ui.no_questions') }}</li>
                    @endforelse
                </ol>
            </x-card>
        </div>

        <div class="space-y-6">
            {{-- Share --}}
            <x-card :title="__('ui.share_link')">
                <div class="flex flex-col items-center gap-3">
                    <canvas id="test-qr" data-url="{{ $url }}" width="150" height="150" class="rounded-lg ring-1 ring-gray-100"></canvas>
                    <input type="text" readonly value="{{ $url }}" dir="ltr" class="block w-full rounded-lg border-0 bg-gray-50 px-3 py-2 text-xs ring-1 ring-inset ring-gray-200">
                    @unless ($test->is_published)
                        <p class="text-center text-xs text-amber-600">{{ __('ui.draft') }}</p>
                    @endunless
                </div>
            </x-card>

            {{-- Results --}}
            <x-card :title="__('ui.attempts')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($attempts as $attempt)
                        <li class="flex items-center justify-between py-2.5 text-sm">
                            <span class="font-medium">{{ $attempt->student->name }}</span>
                            <span class="tabular-nums">{{ (float) $attempt->score }}/{{ (float) $attempt->max_score }} · {{ $attempt->percentage() }}%</span>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-gray-500">{{ __('ui.no_results') }}</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>

    <div class="mt-6">
        <x-button variant="secondary" :href="route('tenant.tests.index')">{{ __('ui.back') }}</x-button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        (function () {
            const qr = document.getElementById('test-qr');
            if (qr && window.QRious) { new QRious({ element: qr, value: qr.dataset.url, size: 150 }); }

            const type = document.getElementById('q-type');
            const mcq = document.getElementById('mcq-block');
            const tf = document.getElementById('tf-block');
            function sync() {
                const isTf = type.value === 'true_false';
                tf.hidden = !isTf; mcq.hidden = isTf;
                // Only the active block's "correct" input should submit under the name "correct".
                tf.querySelectorAll('input').forEach(i => { i.disabled = !isTf; i.name = isTf ? 'correct' : 'correct_tf'; });
                mcq.querySelectorAll('input[type=radio]').forEach(i => { i.disabled = isTf; });
            }
            type.addEventListener('change', sync); sync();
        })();
    </script>
</x-layouts.app>
