<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum BillingPeriod: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Yearly = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => __('plans.period.monthly'),
            self::Quarterly => __('plans.period.quarterly'),
            self::Yearly => __('plans.period.yearly'),
        };
    }

    /**
     * Number of months a single billing cycle covers.
     */
    public function months(): int
    {
        return match ($this) {
            self::Monthly => 1,
            self::Quarterly => 3,
            self::Yearly => 12,
        };
    }

    /**
     * Add one billing cycle to the given date.
     */
    public function advance(Carbon $from): Carbon
    {
        return $from->copy()->addMonths($this->months());
    }
}
