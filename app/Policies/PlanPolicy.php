<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;

class PlanPolicy
{
    /**
     * Only super admins manage subscription plans.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Plan $plan): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Plan $plan): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Plan $plan): bool
    {
        return $user->isSuperAdmin() && $plan->subscriptions()->doesntExist();
    }
}
