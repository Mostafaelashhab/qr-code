<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-form.field name="name" :label="__('ui.name')" :value="$subject->name" required />
        <x-form.field name="stage" :label="__('ui.stage')" :value="$subject->stage" />
        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$subject->is_active ?? true" />
        </div>
    </div>
</x-card>
