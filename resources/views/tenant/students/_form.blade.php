<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-form.field name="name" :label="__('ui.name')" :value="$student->name" required />
        <x-form.field name="stage" :label="__('ui.stage')" :value="$student->stage" />
        <x-form.field name="phone" :label="__('ui.phone')" :value="$student->phone" />
        <x-form.field name="guardian_phone" :label="__('ui.guardian_phone')" :value="$student->guardian_phone" />
        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$student->is_active ?? true" />
        </div>
        <div class="sm:col-span-2">
            <x-form.checkbox name="reminders_opt_out" :label="__('ui.reminders_opt_out')" :checked="$student->reminders_opt_out ?? false" />
            <p class="mt-1 text-xs text-gray-500">{{ __('ui.reminders_opt_out_hint') }}</p>
        </div>
    </div>
</x-card>
