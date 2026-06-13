<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Block tenant users whose client account is disabled or whose subscription is inactive.
     *
     * Super admins always pass. Tenant users are redirected to a "subscription inactive"
     * notice instead of seeing the protected area.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->client?->is_active) {
            abort(403, __('messages.account_disabled'));
        }

        if (! $user->client->hasActiveSubscription()) {
            return redirect()->route('tenant.subscription.inactive');
        }

        return $next($request);
    }
}
