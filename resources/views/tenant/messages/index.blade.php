<x-layouts.app :title="__('ui.messages')">
    <x-page-header :title="__('ui.messages')" :subtitle="number_format($messages->total()).' '.__('ui.messages')" />

    @if ($messages->isEmpty())
        <x-card class="!p-0">
            <x-empty-state :title="__('ui.no_messages')" />
        </x-card>
    @else
        {{-- Desktop: table --}}
        <x-data-table class="hidden md:block">
            <x-slot:head>
                <th class="px-6 py-3.5 text-start">{{ __('ui.recipient') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.type') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('messages_log.channel_label') }}</th>
                <th class="px-6 py-3.5 text-start">{{ __('ui.message') }}</th>
                <th class="px-6 py-3.5 text-end">{{ __('ui.created_at') }}</th>
            </x-slot:head>

            @foreach ($messages as $message)
                <tr class="transition hover:bg-gray-50/70">
                    <td class="px-6 py-3.5">
                        <div class="font-medium text-gray-900">{{ $message->student?->name ?? $message->to }}</div>
                        <div class="text-xs text-gray-500" dir="ltr">{{ $message->to }}</div>
                    </td>
                    <td class="px-6 py-3.5"><x-badge color="gray">{{ $message->type->label() }}</x-badge></td>
                    <td class="px-6 py-3.5">
                        <x-badge :color="$message->channel === \App\Enums\MessageChannel::WhatsApp ? 'emerald' : 'gray'">{{ $message->channel->label() }}</x-badge>
                    </td>
                    <td class="px-6 py-3.5 max-w-xs truncate text-gray-600">{{ $message->body }}</td>
                    <td class="px-6 py-3.5 text-end text-xs text-gray-500">{{ $message->created_at->isoFormat('LL') }}</td>
                </tr>
            @endforeach

            <x-slot:footer>{{ $messages->links() }}</x-slot:footer>
        </x-data-table>

        {{-- Mobile: stacked cards --}}
        <div class="space-y-3 md:hidden">
            @foreach ($messages as $message)
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/70">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $message->student?->name ?? $message->to }}</p>
                            <p class="truncate text-xs text-gray-500" dir="ltr">{{ $message->to }}</p>
                        </div>
                        <x-badge :color="$message->channel === \App\Enums\MessageChannel::WhatsApp ? 'emerald' : 'gray'">{{ $message->channel->label() }}</x-badge>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">{{ $message->body }}</p>
                    <div class="mt-2 flex items-center justify-between text-xs text-gray-400">
                        <x-badge color="gray">{{ $message->type->label() }}</x-badge>
                        <span>{{ $message->created_at->isoFormat('LL') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 md:hidden">{{ $messages->links() }}</div>
    @endif
</x-layouts.app>
