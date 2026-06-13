<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Records an activity log entry whenever the model is created, updated or
 * deleted by an authenticated user. Actions performed without an authenticated
 * user (seeders, console, queued jobs) are not logged.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::{$event}(function (Model $model) use ($event): void {
                if (! Auth::check()) {
                    return;
                }

                ActivityLog::create([
                    'client_id' => $model->client_id ?? Auth::user()->client_id,
                    'user_id' => Auth::id(),
                    'action' => $event,
                    'subject_type' => $model->getMorphClass(),
                    'subject_id' => $model->getKey(),
                    'description' => $model->activityDescription($event),
                ]);
            });
        }
    }

    /**
     * Human-readable description of the activity. Override per model if needed.
     */
    public function activityDescription(string $event): string
    {
        $label = $this->activityLabel ?? Str::headline(class_basename($this));

        return trim("{$label} ".($this->activityTitle() ?? '#'.$this->getKey()));
    }

    /**
     * A short title for the subject; override to use a name column.
     */
    public function activityTitle(): ?string
    {
        return $this->name ?? null;
    }
}
