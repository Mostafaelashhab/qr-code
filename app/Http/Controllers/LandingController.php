<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Public marketing landing page. Authenticated users go straight to their dashboard.
     */
    public function __invoke(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('landing', [
            'plans' => Plan::active()->orderBy('sort_order')->orderBy('price')->get(),
        ]);
    }
}
