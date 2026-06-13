<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Clients\CreateClientWithOwner;
use App\Actions\Subscriptions\StartTrial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, CreateClientWithOwner $createClient, StartTrial $startTrial): RedirectResponse
    {
        $client = $createClient->execute($request->clientData(), $request->ownerData());

        // Give the new center a free trial so it can start using the system immediately.
        $startTrial->execute($client);

        Auth::login($client->users()->first());
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
