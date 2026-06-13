<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('subscriptions.status.pending'),
            self::Active => __('subscriptions.status.active'),
            self::Expired => __('subscriptions.status.expired'),
            self::Cancelled => __('subscriptions.status.cancelled'),
        };
    }

    /**
     * Tailwind color token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Active => 'emerald',
            self::Expired => 'rose',
            self::Cancelled => 'gray',
        };
    }

    public function grantsAccess(): bool
    {
        return $this === self::Active;
    }
}
