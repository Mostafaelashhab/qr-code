<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreRoleRequest;
use App\Http\Requests\Tenant\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')
            ->with('rolePermissions')
            ->latest()
            ->paginate(15);

        return view('tenant.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('tenant.roles.create', [
            'role' => new Role,
            'permissions' => Permission::all(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create($request->roleAttributes());
        $role->syncPermissions($request->permissions());

        return redirect()
            ->route('tenant.roles.index')
            ->with('status', __('messages.role_saved'));
    }

    public function edit(Role $role): View
    {
        $role->load('rolePermissions');

        return view('tenant.roles.edit', [
            'role' => $role,
            'permissions' => Permission::all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->roleAttributes());
        $role->syncPermissions($request->permissions());

        return redirect()
            ->route('tenant.roles.index')
            ->with('status', __('messages.role_saved'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->withErrors(['delete' => __('messages.role_has_users')]);
        }

        $role->delete();

        return redirect()
            ->route('tenant.roles.index')
            ->with('status', __('messages.role_deleted'));
    }
}
