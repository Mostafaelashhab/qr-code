<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'clients' => Client::count(),
            'active_clients' => Client::active()->count(),
            'plans' => Plan::count(),
            'active_subscriptions' => Subscription::where('status', SubscriptionStatus::Active)->count(),
        ];

        $recentClients = Client::with('latestSubscription.plan')
            ->latest()
            ->take(5)
            ->get();

        $expiringSoon = Subscription::with(['client', 'plan'])
            ->where('status', SubscriptionStatus::Active)
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays(14)])
            ->orderBy('ends_at')
            ->take(5)
            ->get();

        $clientsByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $clientsByMonth[$month->isoFormat('MMM')] = Client::whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count();
        }

        return view('admin.dashboard', compact('stats', 'recentClients', 'expiringSoon', 'clientsByMonth'));
    }
}
