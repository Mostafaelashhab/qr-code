<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Persist the chosen locale in the session and return to the previous page.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, config('app.supported_locales', []), true)) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
