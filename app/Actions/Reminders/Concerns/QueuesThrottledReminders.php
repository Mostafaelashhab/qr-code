<?php

namespace App\Actions\Reminders\Concerns;

use App\Jobs\SendReminderMessage;
use App\Models\SmsMessage;

/**
 * Queues reminder messages spaced out over time instead of sending them inline
 * all at once. Each message is dispatched with a growing cursor: a random gap
 * (min_delay..max_delay) but never faster than per_minute messages a minute.
 */
trait QueuesThrottledReminders
{
    /**
     * Queue one reminder and advance the shared delay cursor.
     */
    protected function dispatchReminder(SmsMessage $message, int &$cursorSeconds): void
    {
        SendReminderMessage::dispatch($message->id)
            ->onQueue(config('whatsapp.queue', 'whatsapp'))
            ->delay(now()->addSeconds($cursorSeconds));

        $cursorSeconds += $this->reminderSpacingSeconds();
    }

    /**
     * Seconds to wait before the next message in the batch.
     */
    protected function reminderSpacingSeconds(): int
    {
        if (! config('whatsapp.throttle.enabled', true)) {
            return 0;
        }

        $min = (int) config('whatsapp.throttle.min_delay', 4);
        $max = (int) config('whatsapp.throttle.max_delay', 15);
        $perMinute = max(1, (int) config('whatsapp.throttle.per_minute', 8));
        $floor = (int) ceil(60 / $perMinute);

        return max(random_int(min($min, $max), max($min, $max)), $floor);
    }
}
