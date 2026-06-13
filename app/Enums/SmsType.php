<?php

namespace App\Enums;

enum SmsType: string
{
    case General = 'general';
    case Absence = 'absence';
    case PaymentDue = 'payment_due';

    public function label(): string
    {
        return match ($this) {
            self::General => __('messages_log.type.general'),
            self::Absence => __('messages_log.type.absence'),
            self::PaymentDue => __('messages_log.type.payment_due'),
        };
    }
}
