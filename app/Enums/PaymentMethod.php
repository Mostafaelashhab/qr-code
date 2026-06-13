<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Transfer = 'transfer';
    case Wallet = 'wallet';

    public function label(): string
    {
        return match ($this) {
            self::Cash => __('payments.method.cash'),
            self::Card => __('payments.method.card'),
            self::Transfer => __('payments.method.transfer'),
            self::Wallet => __('payments.method.wallet'),
        };
    }
}
