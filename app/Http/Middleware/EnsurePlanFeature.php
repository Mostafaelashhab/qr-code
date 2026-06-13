<?php

namespace App\Http\Middleware;

use App\Enums\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanFeature
{
    /**
     * Allow the request only when the tenant's plan grants the given feature.
     *
     * Usage: ->middleware('feature:exams'). Super admins always pass.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->client?->hasFeature(Feature::from($feature))) {
            abort(403, __('messages.feature_unavailable'));
        }

        return $next($request);
    }
}
