@php use App\Enums\SubscriptionStatus; @endphp

<x-layouts.app :title="__('ui.subscriptions')">
    <x-page-header :title="__('ui.subscriptions')" :subtitle="number_format($subscriptions->total()).' '.__('ui.subscriptions')">
        <x-slot:actions>
            <x-button :href="route('admin.subscriptions.create')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                {{ __('ui.new_subscription') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-filter-bar>
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-gray-500">{{ __('ui.status') }}</label>
            <select name="status" id="status" onchange="this.form.submit()"
                    class="rounded-xl border-0 bg-gray-50 px-3 py-2 text-sm ring-1 ring-inset ring-gray-200 focus:bg-white focus:ring-2 focus:ring-indigo-600">
                <option value="">{{ __('ui.all') }}</option>
                @foreach (SubscriptionStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </x-filter-bar>

    @if ($subscriptions->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.no_results')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.center') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.plan') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.end_date') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.status') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.actions') }}</th>
            </x-slot:head>

            @foreach ($subscriptions as $subscription)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $subscription->client->name }}</td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $subscription->plan->name }}</td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $subscription->ends_at?->isoFormat('LL') ?? '∞' }}</td>
                    <td class="px-6 py-3.5"><x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge></td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="{{ route('admin.subscriptions.renew', $subscription) }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-emerald-600 hover:underline">{{ __('ui.renew') }}</button>
                            </form>
                            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                        </div>
                    </td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $subscriptions->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($subscriptions as $subscription)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $subscription->client->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $subscription->plan->name }} · {{ $subscription->ends_at?->isoFormat('LL') ?? '∞' }}</p>
                        </div>
                        <x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-4 border-t border-gray-100 pt-3 text-xs">
                        <form method="POST" action="{{ route('admin.subscriptions.renew', $subscription) }}">
                            @csrf
                            <button type="submit" class="font-medium text-emerald-600">{{ __('ui.renew') }}</button>
                        </form>
                        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="font-medium text-indigo-600">{{ __('ui.edit') }}</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $subscriptions->links() }}</div>
    @endif
</x-layouts.app>
