<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:expire')]
#[Description('Mark active subscriptions whose end date has passed as expired.')]
class ExpireSubscriptions extends Command
{
    public function handle(): int
    {
        $count = Subscription::query()
            ->where('status', SubscriptionStatus::Active)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->update(['status' => SubscriptionStatus::Expired]);

        $this->info("Expired {$count} subscription(s).");

        return self::SUCCESS;
    }
}
