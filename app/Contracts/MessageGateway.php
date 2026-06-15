<?php

namespace App\Contracts;

use App\Enums\MessageChannel;
use App\Models\Client;

/**
 * Transport that delivers a reminder message on behalf of a specific center.
 *
 * The center is explicit (not implied from auth) so the gateway can resolve the
 * right WhatsApp session — reminders may be dispatched from queued jobs or the
 * scheduler where there is no authenticated user. Recording the message in the
 * outbox is the caller's responsibility.
 */
interface MessageGateway
{
    /**
     * Deliver a message to the given number for the given center.
     * Returns whether the send succeeded.
     */
    public function send(Client $client, string $to, string $body): bool;

    /**
     * The channel this gateway delivers over (recorded on each outbox row).
     */
    public function channel(): MessageChannel;
}
