@php
    $subjectOptions = $subjects->mapWithKeys(fn ($s) => [$s->id => $s->name])->all();
    $teacherOptions = $teachers->mapWithKeys(fn ($t) => [$t->id => $t->name])->all();
@endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-form.field name="name" :label="__('ui.name')" :value="$group->name" required />
        </div>
        <x-form.select name="subject_id" :label="__('ui.subject')" :options="$subjectOptions"
                       :selected="$group->subject_id" :placeholder="'—'" required />
        <x-form.select name="teacher_id" :label="__('ui.teacher')" :options="$teacherOptions"
                       :selected="$group->teacher_id" :placeholder="__('ui.none')" />
        <x-form.field name="monthly_fee" :label="__('ui.monthly_fee')" type="number" step="0.01" min="0"
                      :value="$group->monthly_fee ?? 0" required />
        <x-form.field name="teacher_share" :label="__('ui.teacher_share')" type="number" step="0.01" min="0" max="100"
                      :value="$group->teacher_share ?? 0" :hint="__('ui.percent_of_revenue')" />
        <x-form.field name="capacity" :label="__('ui.capacity')" type="number" min="1" :value="$group->capacity" />
        <div class="sm:col-span-2">
            <x-form.field name="schedule" :label="__('ui.schedule')" :value="$group->schedule" />
        </div>
        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$group->is_active ?? true" />
        </div>
    </div>
</x-card>
