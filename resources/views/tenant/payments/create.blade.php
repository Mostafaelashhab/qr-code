@php
    use App\Enums\PaymentMethod;

    $studentOptions = $students->mapWithKeys(fn ($s) => [$s->id => $s->name])->all();
    $groupOptions = $groups->mapWithKeys(fn ($g) => [$g->id => $g->name])->all();
    $methodOptions = collect(PaymentMethod::cases())->mapWithKeys(fn ($m) => [$m->value => $m->label()])->all();
@endphp

<x-layouts.app :title="__('ui.new_payment')">
    <div class="mx-auto max-w-xl">
        <form method="POST" action="{{ route('tenant.payments.store') }}" class="space-y-6">
            @csrf
            <x-card>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form.select name="student_id" :label="__('ui.student')" :options="$studentOptions"
                                       :selected="$selectedStudent" :placeholder="'—'" required />
                    </div>
                    <x-form.select name="group_id" :label="__('ui.group')" :options="$groupOptions" :placeholder="__('ui.none')" />
                    <x-form.select name="method" :label="__('ui.method')" :options="$methodOptions" :selected="'cash'" required />
                    <x-form.field name="amount" :label="__('ui.amount')" type="number" step="0.01" min="0" required />
                    <x-form.field name="for_month" :label="__('ui.for_month')" type="month" :value="now()->format('Y-m')" />
                    <x-form.field name="paid_at" :label="__('ui.paid_at')" type="date" :value="now()->toDateString()" required />
                    <div class="sm:col-span-2">
                        <x-form.field name="note" :label="__('ui.notes')" />
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('tenant.payments.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.create') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
