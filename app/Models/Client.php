<?php

namespace App\Models;

use App\Enums\Feature;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo_path',
        'currency',
        'timezone',
        'default_monthly_fee',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
        'currency' => 'EGP',
        'timezone' => 'Africa/Cairo',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'default_monthly_fee' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return HasOne<WhatsAppSession, $this>
     */
    public function whatsappSession(): HasOne
    {
        return $this->hasOne(WhatsAppSession::class);
    }

    /**
     * The most recent subscription, regardless of status.
     *
     * @return HasOne<Subscription, $this>
     */
    public function latestSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * The currently active subscription, if any.
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()->active()->latest('starts_at')->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * The plan backing the client's active subscription, if any.
     */
    public function currentPlan(): ?Plan
    {
        return $this->activeSubscription()?->loadMissing('plan')->plan;
    }

    /**
     * Whether the client may still add another login user under its plan.
     */
    public function canAddUser(): bool
    {
        $limit = $this->currentPlan()?->max_users;

        return $limit === null || $this->users()->count() < $limit;
    }

    /**
     * Whether the client may still add another student under its plan.
     */
    public function canAddStudent(): bool
    {
        $limit = $this->currentPlan()?->max_students;

        return $limit === null
            || Student::withoutGlobalScopes()->where('client_id', $this->id)->count() < $limit;
    }

    /**
     * Whether the client is currently on a free trial.
     */
    public function isOnTrial(): bool
    {
        return (bool) $this->activeSubscription()?->isTrial();
    }

    /**
     * Whether the client's active plan grants the given optional feature.
     */
    public function hasFeature(Feature $feature): bool
    {
        // WhatsApp linking is not offered during the free trial.
        if ($feature === Feature::WhatsApp && $this->isOnTrial()) {
            return false;
        }

        return (bool) $this->currentPlan()?->includesFeature($feature);
    }
}
