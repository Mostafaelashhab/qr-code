<?php

namespace App\Models;

use App\Enums\Permission;
use App\Models\Concerns\BelongsToClient;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'name',
        'is_default',
    ];

    protected $attributes = [
        'is_default' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return HasMany<RolePermission, $this>
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * The permissions granted to this role.
     *
     * @return Collection<int, Permission>
     */
    public function permissions(): Collection
    {
        return $this->rolePermissions
            ->pluck('permission')
            ->map(fn (Permission $permission): Permission => $permission);
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->rolePermissions
            ->contains(fn (RolePermission $record): bool => $record->permission === $permission);
    }

    /**
     * Replace this role's permissions with the given set.
     *
     * @param  array<int, Permission|string>  $permissions
     */
    public function syncPermissions(array $permissions): void
    {
        $values = collect($permissions)
            ->map(fn (Permission|string $permission): string => $permission instanceof Permission ? $permission->value : $permission)
            ->unique()
            ->values();

        $this->rolePermissions()->whereNotIn('permission', $values)->delete();

        $existing = $this->rolePermissions()->pluck('permission')->map(
            fn (Permission $permission): string => $permission->value,
        );

        foreach ($values->diff($existing) as $permission) {
            $this->rolePermissions()->create(['permission' => $permission]);
        }

        $this->load('rolePermissions');
    }
}
