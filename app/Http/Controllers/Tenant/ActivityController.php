<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $activities = ActivityLog::with('user')
            ->where('client_id', $request->user()->client_id)
            ->latest()
            ->paginate(30);

        return view('tenant.activity.index', compact('activities'));
    }
}
