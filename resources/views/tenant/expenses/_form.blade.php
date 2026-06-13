@php
    use App\Enums\ExpenseCategory;
    $categoryOptions = collect(ExpenseCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all();
@endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-form.field name="title" :label="__('ui.title')" :value="$expense->title" required />
        </div>
        <x-form.select name="category" :label="__('ui.category')" :options="$categoryOptions"
                       :selected="$expense->category?->value ?? 'other'" required />
        <x-form.field name="amount" :label="__('ui.amount')" type="number" step="0.01" min="0" :value="$expense->amount" required />
        <x-form.field name="spent_at" :label="__('ui.spent_at')" type="date"
                      :value="$expense->spent_at?->toDateString() ?? now()->toDateString()" required />
        <div></div>
        <div class="sm:col-span-2">
            <x-form.field name="note" :label="__('ui.notes')" :value="$expense->note" />
        </div>
    </div>
</x-card>
