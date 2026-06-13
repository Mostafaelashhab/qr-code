<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    /**
     * Super admins manage all subscriptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * A super admin sees any subscription; a tenant user sees only their own client's.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->isSuperAdmin()
            || $subscription->client_id === $user->client_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->isSuperAdmin();
    }
}
