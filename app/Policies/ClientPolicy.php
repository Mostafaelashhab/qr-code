<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Only super admins manage clients (tenants).
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Client $client): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Client $client): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->isSuperAdmin();
    }
}
