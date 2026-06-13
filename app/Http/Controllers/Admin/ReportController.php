<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Platform-wide analytics for the super admin: subscription revenue,
     * a per-plan breakdown, client status counts and upcoming expiries.
     */
    public function __invoke(): View
    {
        $activeSubscriptions = Subscription::where('status', SubscriptionStatus::Active)->get();

        $stats = [
            'active_subscriptions' => $activeSubscriptions->count(),
            'subscription_revenue' => (float) $activeSubscriptions->sum('price'),
            'active_clients' => Client::where('is_active', true)->count(),
            'inactive_clients' => Client::where('is_active', false)->count(),
        ];

        $revenueByPlan = Plan::query()
            ->withCount(['subscriptions as active_count' => fn ($query) => $query->where('status', SubscriptionStatus::Active)])
            ->withSum(['subscriptions as active_revenue' => fn ($query) => $query->where('status', SubscriptionStatus::Active)], 'price')
            ->orderBy('sort_order')
            ->get();

        $expiringSoon = Subscription::with(['client', 'plan'])
            ->where('status', SubscriptionStatus::Active)
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays(30)])
            ->orderBy('ends_at')
            ->get();

        return view('admin.reports.index', compact('stats', 'revenueByPlan', 'expiringSoon'));
    }
}
