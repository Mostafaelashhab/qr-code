<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = SmsMessage::with('student')->latest()->paginate(20);

        return view('tenant.messages.index', compact('messages'));
    }
}
