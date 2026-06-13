<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreUserRequest;
use App\Http\Requests\Tenant\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::forClient($request->user()->client_id)
            ->latest()
            ->paginate(15);

        return view('tenant.users.index', compact('users'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('tenant.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        if (! $request->user()->client->canAddUser()) {
            return back()->withInput()->withErrors(['name' => __('messages.plan_user_limit')]);
        }

        $request->user()->client->users()->create($request->validated());

        return redirect()
            ->route('tenant.users.index')
            ->with('status', __('messages.user_created'));
    }

    public function edit(Request $request, User $user): View
    {
        $this->ensureSameTenant($request, $user);
        Gate::authorize('update', $user);

        return view('tenant.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->ensureSameTenant($request, $user);
        Gate::authorize('update', $user);

        $user->update($request->userAttributes());

        return redirect()
            ->route('tenant.users.index')
            ->with('status', __('messages.user_updated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureSameTenant($request, $user);
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('tenant.users.index')
            ->with('status', __('messages.user_deleted'));
    }

    /**
     * Guard against a client admin touching a user outside their own tenant.
     */
    private function ensureSameTenant(Request $request, User $user): void
    {
        abort_unless($user->client_id === $request->user()->client_id, 404);
    }
}
