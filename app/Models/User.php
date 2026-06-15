<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['client_id', 'name', 'email', 'role', 'role_id', 'phone', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'role' => UserRole::ClientUser->value,
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
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
     * The custom staff role assigned to this user, if any. Center admins and
     * super admins have no custom role — they implicitly hold every permission.
     *
     * @return BelongsTo<Role, $this>
     */
    public function staffRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to the users belonging to a given client (tenant).
     */
    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isClientAdmin(): bool
    {
        return $this->role === UserRole::ClientAdmin;
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Whether this user may access the given module.
     *
     * Super admins and center admins hold every permission. Other staff hold
     * only the permissions granted by their assigned custom role.
     */
    public function hasPermission(Permission $permission): bool
    {
        if ($this->isSuperAdmin() || $this->isClientAdmin()) {
            return true;
        }

        return (bool) $this->staffRole?->hasPermission($permission);
    }

    /**
     * Whether this user may access the tenant area (active account and active subscription).
     */
    public function hasActiveTenant(): bool
    {
        return $this->isSuperAdmin()
            || ((bool) $this->client?->is_active && $this->client->hasActiveSubscription());
    }
}
