<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plan_id',
        'status',
        'price',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'notes',
    ];

    protected $attributes = [
        'status' => SubscriptionStatus::Pending->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'price' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::Active)
            ->where(function (Builder $query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Whether the subscription is active and not past its end date.
     */
    public function isCurrentlyActive(): bool
    {
        return $this->status === SubscriptionStatus::Active
            && (! $this->ends_at || $this->ends_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatus::Expired
            || ($this->ends_at !== null && $this->ends_at->isPast());
    }

    /**
     * Whether this is a free trial subscription (granted at no charge,
     * for a limited period, on the cheapest active plan).
     */
    public function isTrial(): bool
    {
        return (float) $this->price === 0.0 && $this->ends_at !== null;
    }

    /**
     * Days remaining until the subscription ends (null when open-ended).
     */
    public function daysRemaining(): ?int
    {
        if ($this->ends_at === null) {
            return null;
        }

        return max(0, Carbon::now()->diffInDays($this->ends_at, false));
    }
}
