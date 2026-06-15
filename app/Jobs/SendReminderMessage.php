<?php

namespace App\Jobs;

use App\Contracts\MessageGateway;
use App\Models\SmsMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Delivers a single queued reminder. Dispatched one per recipient with a
 * growing delay so a batch is spaced out instead of blasted at once — bulk,
 * instant sends are the fastest way to get a WhatsApp number flagged.
 */
class SendReminderMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $messageId) {}

    public function handle(MessageGateway $gateway): void
    {
        $message = SmsMessage::withoutGlobalScopes()
            ->with('client')
            ->find($this->messageId);

        if ($message === null || $message->client === null) {
            return;
        }

        $ok = $gateway->send($message->client, $message->to, $message->body);

        $message->update([
            'status' => $ok ? 'sent' : 'failed',
            'sent_at' => now(),
        ]);
    }
}
