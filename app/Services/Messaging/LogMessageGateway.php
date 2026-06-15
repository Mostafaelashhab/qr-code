<?php

namespace App\Services\Messaging;

use App\Contracts\MessageGateway;
use App\Enums\MessageChannel;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

/**
 * Default gateway for local/testing: records the message to the log instead of
 * contacting WhatsApp. Production swaps this for WhatsAppGateway via the
 * "whatsapp.driver" config.
 */
class LogMessageGateway implements MessageGateway
{
    public function send(Client $client, string $to, string $body): bool
    {
        Log::info('WhatsApp message dispatched (log driver)', [
            'client_id' => $client->id,
            'to' => $to,
            'body' => $body,
        ]);

        return true;
    }

    public function channel(): MessageChannel
    {
        return MessageChannel::WhatsApp;
    }
}
