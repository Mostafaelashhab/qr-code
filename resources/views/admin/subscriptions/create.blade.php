@php
    $clientOptions = $clients->mapWithKeys(fn ($client) => [$client->id => $client->name])->all();
    $planOptions = $plans->mapWithKeys(fn ($plan) => [$plan->id => $plan->name.' — '.number_format((float) $plan->price, 2).' / '.$plan->billing_period->label()])->all();
@endphp

<x-layouts.app :title="__('ui.new_subscription')">
    <div class="mx-auto max-w-xl">
        <x-page-header :title="__('ui.new_subscription')" :breadcrumbs="[
            ['label' => __('ui.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('ui.subscriptions'), 'url' => route('admin.subscriptions.index')],
            ['label' => __('ui.new_subscription')],
        ]" />

        <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="space-y-6">
            @csrf

            <x-card>
                <div class="space-y-5">
                    <x-form.select name="client_id" :label="__('ui.center')" :options="$clientOptions"
                                   :selected="$selectedClient" :placeholder="'—'" required />
                    <x-form.select name="plan_id" :label="__('ui.plan')" :options="$planOptions" :placeholder="'—'" required />
                    <x-form.field name="starts_at" :label="__('ui.start_date')" type="date" />
                    <x-form.field name="price" :label="__('ui.price')" type="number" step="0.01" min="0"
                                  :hint="__('ui.optional')" />
                    <x-form.textarea name="notes" :label="__('ui.notes')" />
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.subscriptions.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.start_subscription') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
