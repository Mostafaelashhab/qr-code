<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Route the authenticated user to the dashboard for their role.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            UserRole::SuperAdmin => redirect()->route('admin.dashboard'),
            default => redirect()->route('tenant.dashboard'),
        };
    }
}
