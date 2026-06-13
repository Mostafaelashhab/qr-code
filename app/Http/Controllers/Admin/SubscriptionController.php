<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Subscriptions\RenewSubscription;
use App\Actions\Subscriptions\StartSubscription;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionRequest;
use App\Http\Requests\Admin\UpdateSubscriptionRequest;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Subscription::class);

        $subscriptions = Subscription::query()
            ->with(['client', 'plan'])
            ->when($request->enum('status', SubscriptionStatus::class), function ($query, SubscriptionStatus $status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Subscription::class);

        $clients = Client::active()->orderBy('name')->get();
        $plans = Plan::active()->orderBy('sort_order')->get();
        $selectedClient = $request->integer('client_id') ?: null;

        return view('admin.subscriptions.create', compact('clients', 'plans', 'selectedClient'));
    }

    public function store(StoreSubscriptionRequest $request, StartSubscription $action): RedirectResponse
    {
        $client = Client::findOrFail($request->integer('client_id'));
        $plan = Plan::findOrFail($request->integer('plan_id'));

        $action->execute(
            client: $client,
            plan: $plan,
            startsAt: $request->filled('starts_at') ? Carbon::parse($request->date('starts_at')) : null,
            price: $request->filled('price') ? (float) $request->input('price') : null,
            notes: $request->input('notes'),
        );

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('status', __('messages.subscription_started'));
    }

    public function edit(Subscription $subscription): View
    {
        Gate::authorize('update', $subscription);

        $subscription->load(['client', 'plan']);

        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $subscription->update($request->validated());

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('status', __('messages.subscription_updated'));
    }

    public function renew(Subscription $subscription, RenewSubscription $action): RedirectResponse
    {
        Gate::authorize('update', $subscription);

        $action->execute($subscription);

        return back()->with('status', __('messages.subscription_renewed'));
    }

    public function cancel(Subscription $subscription): RedirectResponse
    {
        Gate::authorize('update', $subscription);

        $subscription->update([
            'status' => SubscriptionStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        return back()->with('status', __('messages.subscription_cancelled'));
    }
}
