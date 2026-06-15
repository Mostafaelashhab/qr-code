<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSession;
use App\Services\WhatsApp\WaapiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Center-facing WhatsApp linking page. The super admin provisions the waapi
 * device; here the center scans the QR to link its number and sees live status.
 */
class WhatsAppController extends Controller
{
    public function __construct(private readonly WaapiClient $client) {}

    public function show(Request $request): View
    {
        $session = $this->sessionFor($request);

        return view('tenant.whatsapp.show', compact('session'));
    }

    /**
     * Polled by the page to refresh the QR + connection status. Returns a
     * "not provisioned yet" state until the super admin sets up the device.
     */
    public function qr(Request $request): JsonResponse
    {
        $session = $this->sessionFor($request);

        if (! $session->isProvisioned()) {
            return response()->json([
                'provisioned' => false,
                'connected' => false,
                'qr' => null,
            ]);
        }

        $status = $this->client->status($session);

        $session->status = $status;
        if ($status->isConnected()) {
            $session->last_connected_at = now();
        }
        $session->save();

        return response()->json([
            'provisioned' => true,
            'status' => $status->value,
            'status_label' => $status->label(),
            'connected' => $status->isConnected(),
            'qr' => $status->isConnected() ? null : $this->client->qr($session),
        ]);
    }

    private function sessionFor(Request $request): WhatsAppSession
    {
        return WhatsAppSession::firstOrCreate(['client_id' => $request->user()->client_id]);
    }
}
