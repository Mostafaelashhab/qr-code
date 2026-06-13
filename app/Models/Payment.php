<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\LogsActivity;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use BelongsToClient, HasFactory, LogsActivity;

    /**
     * Activity log label for payments.
     */
    public function activityTitle(): ?string
    {
        return '#'.$this->getKey();
    }

    protected $fillable = [
        'student_id',
        'group_id',
        'amount',
        'method',
        'for_month',
        'paid_at',
        'note',
    ];

    protected $attributes = [
        'method' => PaymentMethod::Cash->value,
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'method' => PaymentMethod::class,
            'paid_at' => 'date',
        ];
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

    public function scopePaidBetween(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('paid_at', [$from, $to]);
    }
}
