<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\LogsActivity;
use Database\Factories\ChargeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charge extends Model
{
    /** @use HasFactory<ChargeFactory> */
    use BelongsToClient, HasFactory, LogsActivity;

    protected $fillable = [
        'student_id',
        'group_id',
        'title',
        'amount',
        'discount',
        'for_month',
        'due_date',
        'note',
    ];

    protected $attributes = [
        'discount' => 0,
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function activityTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Amount actually owed after the discount.
     */
    public function netAmount(): float
    {
        return (float) $this->amount - (float) $this->discount;
    }
}
