@php
    use App\Enums\SubscriptionStatus;

    $statusOptions = collect(SubscriptionStatus::cases())
        ->mapWithKeys(fn (SubscriptionStatus $status) => [$status->value => $status->label()])
        ->all();
@endphp

<x-layouts.app :title="__('ui.edit_subscription')">
    <div class="mx-auto max-w-xl">
        <x-page-header :title="__('ui.edit_subscription')"
                       :subtitle="$subscription->client->name.' · '.$subscription->plan->name"
                       :breadcrumbs="[
                           ['label' => __('ui.dashboard'), 'url' => route('admin.dashboard')],
                           ['label' => __('ui.subscriptions'), 'url' => route('admin.subscriptions.index')],
                           ['label' => __('ui.edit')],
                       ]" />

        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card>
                <div class="space-y-5">
                    <x-form.select name="status" :label="__('ui.status')" :options="$statusOptions"
                                   :selected="$subscription->status->value" required />
                    <x-form.field name="starts_at" :label="__('ui.start_date')" type="date"
                                  :value="$subscription->starts_at?->toDateString()" />
                    <x-form.field name="ends_at" :label="__('ui.end_date')" type="date"
                                  :value="$subscription->ends_at?->toDateString()" />
                    <x-form.field name="price" :label="__('ui.price')" type="number" step="0.01" min="0"
                                  :value="$subscription->price" required />
                    <x-form.textarea name="notes" :label="__('ui.notes')" :value="$subscription->notes" />
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.subscriptions.index')">{{ __('ui.cancel') }}</x-button>
                <x-button type="submit">{{ __('ui.update') }}</x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
