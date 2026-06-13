<?php

namespace App\Enums;

enum PaymentChannel: string
{
    case InstaPay = 'instapay';
    case VodafoneCash = 'vodafone_cash';

    public function label(): string
    {
        return match ($this) {
            self::InstaPay => __('billing.channel.instapay'),
            self::VodafoneCash => __('billing.channel.vodafone_cash'),
        };
    }

    /**
     * The platform account the center should transfer to for this channel.
     */
    public function receivingAccount(): ?string
    {
        return match ($this) {
            self::InstaPay => config('billing.instapay_address'),
            self::VodafoneCash => config('billing.vodafone_cash_number'),
        };
    }
}
