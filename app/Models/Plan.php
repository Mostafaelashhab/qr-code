<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Enums\Feature;
use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'max_users',
        'max_students',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'billing_period' => BillingPeriod::class,
            'max_users' => 'integer',
            'max_students' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Whether the plan allows unlimited users.
     */
    public function hasUnlimitedUsers(): bool
    {
        return $this->max_users === null;
    }

    /**
     * Whether this plan grants the given optional feature.
     */
    public function includesFeature(Feature $feature): bool
    {
        return in_array($feature->value, $this->features ?? [], true);
    }
}
