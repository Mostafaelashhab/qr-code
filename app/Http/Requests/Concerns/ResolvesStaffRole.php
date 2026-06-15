<?php

namespace App\Http\Requests\Concerns;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * Shared handling of the staff "role_ref" select on the user form.
 *
 * The single dropdown offers "admin" (a full-access center admin) plus every
 * custom role belonging to the current center. This trait validates that choice
 * and resolves it back into the underlying role enum + role_id columns.
 */
trait ResolvesStaffRole
{
    /**
     * Validation rule constraining role_ref to "admin" or one of the center's
     * own custom role ids.
     */
    protected function staffRoleRule(): In
    {
        $roleIds = Role::where('client_id', $this->user()->client_id)
            ->pluck('id')
            ->map(fn (int $id): string => (string) $id)
            ->all();

        return Rule::in(['admin', ...$roleIds]);
    }

    /**
     * Resolve role_ref into the user's role enum + role_id columns.
     *
     * @return array{role: UserRole, role_id: int|null}
     */
    protected function resolvedRole(): array
    {
        $ref = $this->input('role_ref');

        if ($ref === 'admin') {
            return ['role' => UserRole::ClientAdmin, 'role_id' => null];
        }

        return ['role' => UserRole::ClientUser, 'role_id' => (int) $ref];
    }
}
