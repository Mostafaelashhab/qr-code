@php
    $subjectOptions = $subjects->mapWithKeys(fn ($s) => [$s->id => $s->name])->all();
@endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-form.field name="name" :label="__('ui.name')" :value="$teacher->name" required />
        <x-form.select name="subject_id" :label="__('ui.subject')" :options="$subjectOptions"
                       :selected="$teacher->subject_id" :placeholder="__('ui.none')" />
        <x-form.field name="phone" :label="__('ui.phone')" :value="$teacher->phone" />
        <x-form.field name="email" :label="__('ui.email')" type="email" :value="$teacher->email" />
        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$teacher->is_active ?? true" />
        </div>
    </div>
</x-card>
