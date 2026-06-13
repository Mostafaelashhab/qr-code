@php $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all(); @endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-form.field name="title" :label="__('ui.title')" :value="$test->title" required />
        </div>
        <x-form.select name="group_id" :label="__('ui.group')" :options="$groupOptions" :selected="$test->group_id" :placeholder="'—'" required />
        <x-form.field name="duration_minutes" :label="__('ui.duration_minutes')" type="number" min="1" max="600" :value="$test->duration_minutes ?? 30" required />
        <x-form.field name="available_from" :label="__('ui.available_from')" type="datetime-local" :value="$test->available_from?->format('Y-m-d\TH:i')" />
        <x-form.field name="available_to" :label="__('ui.available_to')" type="datetime-local" :value="$test->available_to?->format('Y-m-d\TH:i')" />
        <div class="sm:col-span-2 space-y-2">
            <x-form.checkbox name="shuffle" :label="__('ui.shuffle_questions')" :checked="$test->shuffle ?? true" />
            <x-form.checkbox name="show_results" :label="__('ui.show_results')" :checked="$test->show_results ?? true" />
        </div>
    </div>
</x-card>
