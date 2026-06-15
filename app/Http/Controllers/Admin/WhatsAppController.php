<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\WhatsAppSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Super-admin provisioning of a center's waapi device. The admin creates the
 * device in the waapi dashboard, then pastes its identifiers here so the center
 * can link its number.
 */
class WhatsAppController extends Controller
{
    public function update(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('update', $client);

        $data = $request->validate([
            'auth_key' => ['nullable', 'string', 'max:255'],
            'device_uuid' => ['nullable', 'string', 'max:255'],
            'app_key' => ['nullable', 'string', 'max:255'],
        ]);

        WhatsAppSession::withoutGlobalScopes()->updateOrCreate(
            ['client_id' => $client->id],
            [
                'auth_key' => $data['auth_key'] ?: null,
                'device_uuid' => $data['device_uuid'] ?: null,
                'app_key' => $data['app_key'] ?: null,
            ],
        );

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('status', __('whatsapp.provisioning_saved'));
    }
}
