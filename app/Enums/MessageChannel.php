<?php

namespace App\Enums;

enum MessageChannel: string
{
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::Sms => __('messages_log.channel.sms'),
            self::WhatsApp => __('messages_log.channel.whatsapp'),
        };
    }
}
