<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * Default gateway used in local/testing: it records the SMS to the log instead
 * of contacting a provider. Swap the binding in AppServiceProvider for a real
 * HTTP gateway (Twilio, Vonage, a local aggregator, …) in production.
 */
class LogSmsGateway implements SmsGateway
{
    public function send(string $to, string $body): bool
    {
        Log::info('SMS dispatched', ['to' => $to, 'body' => $body]);

        return true;
    }
}
