<?php

namespace Database\Factories;

use App\Enums\Permission;
use App\Models\Client;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->unique()->jobTitle(),
            'is_default' => false,
        ];
    }

    /**
     * Grant the role the given permissions (defaults to all of them).
     *
     * @param  array<int, Permission|string>|null  $permissions
     */
    public function withPermissions(?array $permissions = null): static
    {
        return $this->afterCreating(function (Role $role) use ($permissions): void {
            $role->syncPermissions($permissions ?? Permission::all());
        });
    }
}
