<?php

namespace App\Models;

use App\Enums\PaymentChannel;
use App\Enums\PaymentRequestStatus;
use App\Models\Concerns\BelongsToClient;
use Database\Factories\SubscriptionPaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    /** @use HasFactory<SubscriptionPaymentFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'plan_id',
        'amount',
        'channel',
        'reference',
        'receipt_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $attributes = [
        'status' => PaymentRequestStatus::Pending->value,
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'channel' => PaymentChannel::class,
            'status' => PaymentRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PaymentRequestStatus::Pending);
    }

    public function isPending(): bool
    {
        return $this->status === PaymentRequestStatus::Pending;
    }
}
