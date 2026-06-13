<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case ClientAdmin = 'client_admin';
    case ClientUser = 'client_user';

    /**
     * Human-readable, translatable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => __('roles.super_admin'),
            self::ClientAdmin => __('roles.client_admin'),
            self::ClientUser => __('roles.client_user'),
        };
    }

    /**
     * Roles that operate inside a tenant (belong to a client).
     *
     * @return array<int, self>
     */
    public static function tenantRoles(): array
    {
        return [self::ClientAdmin, self::ClientUser];
    }

    /**
     * Roles a client admin is allowed to assign to their own users.
     *
     * @return array<int, self>
     */
    public static function assignableByClientAdmin(): array
    {
        return [self::ClientAdmin, self::ClientUser];
    }

    public function isTenantRole(): bool
    {
        return in_array($this, self::tenantRoles(), true);
    }
}
