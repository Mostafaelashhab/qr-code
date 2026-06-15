<?php

namespace App\Enums;

enum WhatsAppStatus: string
{
    case Disconnected = 'disconnected';
    case Connecting = 'connecting';
    case Connected = 'connected';

    public function label(): string
    {
        return match ($this) {
            self::Disconnected => __('whatsapp.status.disconnected'),
            self::Connecting => __('whatsapp.status.connecting'),
            self::Connected => __('whatsapp.status.connected'),
        };
    }

    public function isConnected(): bool
    {
        return $this === self::Connected;
    }
}
