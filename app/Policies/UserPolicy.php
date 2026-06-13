<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Client admins manage the users that belong to their own client (tenant).
     */
    public function viewAny(User $user): bool
    {
        return $user->isClientAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isClientAdmin() && $this->sharesClient($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->isClientAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isClientAdmin() && $this->sharesClient($user, $model);
    }

    /**
     * A client admin may delete other users in their client, but not themselves.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isClientAdmin()
            && $this->sharesClient($user, $model)
            && $user->isNot($model);
    }

    private function sharesClient(User $user, User $model): bool
    {
        return $user->client_id !== null
            && $user->client_id === $model->client_id;
    }
}
