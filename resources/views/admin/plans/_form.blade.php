@php
    use App\Enums\BillingPeriod;
    use App\Enums\Feature;

    $periodOptions = collect(BillingPeriod::cases())
        ->mapWithKeys(fn (BillingPeriod $period) => [$period->value => $period->label()])
        ->all();

    $selectedFeatures = (array) old('features', $plan->features ?? []);
@endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-form.field name="name" :label="__('ui.name')" :value="$plan->name" required />
        </div>

        <x-form.field name="price" :label="__('ui.price')" type="number" step="0.01" min="0" :value="$plan->price" required />
        <x-form.select name="billing_period" :label="__('ui.period')" :options="$periodOptions"
                       :selected="$plan->billing_period?->value ?? 'monthly'" required />

        <x-form.field name="max_users" :label="__('ui.max_users')" type="number" min="1" :value="$plan->max_users"
                      :hint="__('ui.unlimited').' = '.__('ui.optional')" />
        <x-form.field name="max_students" :label="__('ui.students_usage')" type="number" min="1" :value="$plan->max_students"
                      :hint="__('ui.unlimited').' = '.__('ui.optional')" />
        <x-form.field name="sort_order" :label="__('ui.sort_order')" type="number" min="0" :value="$plan->sort_order ?? 0" />

        <div class="sm:col-span-2">
            <x-form.textarea name="description" :label="__('ui.description')" :value="$plan->description" />
        </div>

        <div class="sm:col-span-2 space-y-2">
            <label class="block text-sm font-medium text-gray-700">{{ __('ui.features') }}</label>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach (Feature::cases() as $feature)
                    <label class="flex items-start gap-2.5 rounded-lg px-3 py-2.5 ring-1 ring-gray-200">
                        <input type="checkbox" name="features[]" value="{{ $feature->value }}"
                               @checked(in_array($feature->value, $selectedFeatures, true))
                               class="mt-0.5 size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <span>
                            <span class="block text-sm font-medium text-gray-800">{{ $feature->label() }}</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-gray-500">{{ $feature->description() }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$plan->is_active ?? true" />
        </div>
    </div>
</x-card>
