<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Allow the request only when the authenticated staff member holds the given
     * module permission. Super admins and center admins always pass.
     *
     * Usage: ->middleware('permission:payments')
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        if (! $user->hasPermission(Permission::from($permission))) {
            abort(403, __('messages.permission_denied'));
        }

        return $next($request);
    }
}
