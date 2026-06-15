<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Clients\CreateClientWithOwner;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Client::class);

        $clients = Client::query()
            ->withCount('users')
            ->with('latestSubscription.plan')
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        Gate::authorize('create', Client::class);

        return view('admin.clients.create');
    }

    public function store(StoreClientRequest $request, CreateClientWithOwner $action): RedirectResponse
    {
        $client = $action->execute($request->clientData(), $request->ownerData());

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('status', __('messages.client_created'));
    }

    public function show(Client $client): View
    {
        Gate::authorize('view', $client);

        $client->load(['users', 'subscriptions.plan', 'whatsappSession']);

        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        Gate::authorize('update', $client);

        return view('admin.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('status', __('messages.client_updated'));
    }

    public function destroy(Client $client): RedirectResponse
    {
        Gate::authorize('delete', $client);

        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('status', __('messages.client_deleted'));
    }
}
