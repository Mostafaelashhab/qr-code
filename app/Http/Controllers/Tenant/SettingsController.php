<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('tenant.settings.edit', ['client' => $request->user()->client]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $client = $request->user()->client;
        $data = $request->safe()->except('logo');

        if ($request->hasFile('logo')) {
            if ($client->logo_path) {
                Storage::disk('public')->delete($client->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $client->update($data);

        return redirect()->route('tenant.settings.edit')->with('status', __('messages.settings_saved'));
    }
}
