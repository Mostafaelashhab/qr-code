<?php

namespace App\Services\WhatsApp;

use App\Contracts\MessageGateway;
use App\Enums\MessageChannel;
use App\Models\Client;
use App\Models\WhatsAppSession;

/**
 * Delivers reminder messages over WhatsApp through the waapi provider, using the
 * center's own provisioned device.
 */
class WhatsAppGateway implements MessageGateway
{
    public function __construct(private readonly WaapiClient $client) {}

    public function send(Client $client, string $to, string $body): bool
    {
        $session = WhatsAppSession::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->first();

        if ($session === null) {
            return false;
        }

        return $this->client->send($session, $to, $body);
    }

    public function channel(): MessageChannel
    {
        return MessageChannel::WhatsApp;
    }
}
