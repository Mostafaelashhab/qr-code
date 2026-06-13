<?php

namespace App\Contracts;

interface SmsGateway
{
    /**
     * Deliver an SMS to the given number. Returns whether the send succeeded.
     *
     * Implementations are the transport only; recording the message in the
     * outbox is the caller's responsibility.
     */
    public function send(string $to, string $body): bool;
}
