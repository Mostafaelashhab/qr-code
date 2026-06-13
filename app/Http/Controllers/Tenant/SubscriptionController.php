<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Show the current tenant's subscription history.
     */
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $current = $client->activeSubscription()?->loadMissing('plan');
        $history = $client->subscriptions()
            ->with('plan')
            ->latest('starts_at')
            ->latest()
            ->get();

        return view('tenant.subscription.index', compact('client', 'current', 'history'));
    }

    /**
     * Notice shown when the tenant has no active subscription.
     */
    public function inactive(Request $request): View
    {
        $client = $request->user()->client;

        return view('tenant.subscription.inactive', compact('client'));
    }
}
