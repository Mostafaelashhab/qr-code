<?php

namespace App\Models;

use App\Enums\Weekday;
use App\Models\Concerns\BelongsToClient;
use Database\Factories\TimetableSlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableSlot extends Model
{
    /** @use HasFactory<TimetableSlotFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'group_id',
        'weekday',
        'start_time',
        'end_time',
        'room',
    ];

    protected function casts(): array
    {
        return [
            'weekday' => Weekday::class,
        ];
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Time formatted as HH:MM for display and inputs.
     */
    public function startLabel(): string
    {
        return substr($this->start_time, 0, 5);
    }

    public function endLabel(): string
    {
        return substr($this->end_time, 0, 5);
    }
}
