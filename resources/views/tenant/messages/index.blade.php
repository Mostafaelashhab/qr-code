<x-layouts.app :title="__('ui.messages')">
    <x-card class="overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-start">{{ __('ui.recipient') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.type') }}</th>
                        <th class="px-6 py-3 text-start">{{ __('ui.message') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('ui.created_at') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($messages as $message)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $message->student?->name ?? $message->to }}</div>
                                <div class="text-xs text-gray-500">{{ $message->to }}</div>
                            </td>
                            <td class="px-6 py-4"><x-badge>{{ $message->type->label() }}</x-badge></td>
                            <td class="px-6 py-4 text-gray-600">{{ $message->body }}</td>
                            <td class="px-6 py-4 text-end text-xs text-gray-500">{{ $message->created_at->isoFormat('LL') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">{{ __('ui.no_messages') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $messages->links() }}</div>
</x-layouts.app>
