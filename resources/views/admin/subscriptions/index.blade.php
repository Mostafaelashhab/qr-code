@php use App\Enums\SubscriptionStatus; @endphp

<x-layouts.app :title="__('ui.subscriptions')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()"
                    class="rounded-lg border-0 px-3 py-2 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                <option value="">{{ __('ui.all') }}</option>
                @foreach (SubscriptionStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </form>
        <x-button :href="route('admin.subscriptions.create')">{{ __('ui.new_subscription') }}</x-button>
    </div>

    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.center') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.plan') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.end_date') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.status') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $subscription->client->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $subscription->plan->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $subscription->ends_at?->isoFormat('LL') ?? '∞' }}</td>
                            <td class="px-6 py-4"><x-badge :color="$subscription->status->color()">{{ $subscription->status->label() }}</x-badge></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <form method="POST" action="{{ route('admin.subscriptions.renew', $subscription) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-emerald-600 hover:underline">{{ __('ui.renew') }}</button>
                                    </form>
                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-xs font-medium text-indigo-600 hover:underline">{{ __('ui.edit') }}</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $subscriptions->links() }}</div>
</x-layouts.app>
