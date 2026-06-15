<?php

namespace App\Services\WhatsApp;

use App\Enums\WhatsAppStatus;
use App\Models\WhatsAppSession;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Client for the waapi.octopusteam.net provider.
 *
 * Each center is its own waapi account: it carries its own auth_key plus a
 * device_uuid (for QR/status) and app_key (for sending), all provisioned by the
 * super admin. The docs don't pin down exact response field names, so every
 * parser here is deliberately tolerant.
 */
class WaapiClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
    ) {}

    /**
     * QR image for linking the center's number. Returns an <img src> value
     * (data URL or http URL) or null when none is available yet.
     */
    public function qr(WhatsAppSession $session): ?string
    {
        if (! $session->isProvisioned()) {
            return null;
        }

        $response = $this->request($session->auth_key)->get('/get-qr', [
            'authkey' => $session->auth_key,
            'device_id' => $session->device_uuid,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return $this->extractQr($response->json() ?? []);
    }

    /**
     * Map the provider's device state to our connection status.
     */
    public function status(WhatsAppSession $session): WhatsAppStatus
    {
        if (! $session->isProvisioned()) {
            return WhatsAppStatus::Disconnected;
        }

        $response = $this->request($session->auth_key)->post('/get-status', [
            'authkey' => $session->auth_key,
            'device_id' => $session->device_uuid,
        ]);

        if (! $response->successful()) {
            return WhatsAppStatus::Disconnected;
        }

        $raw = (string) (
            $response->json('status')
            ?? $response->json('data.status')
            ?? $response->json('device.status')
            ?? ''
        );

        return $this->mapStatus($raw);
    }

    /**
     * Send a text message from the center's device.
     */
    public function send(WhatsAppSession $session, string $to, string $body): bool
    {
        if (blank($session->auth_key) || blank($session->app_key)) {
            return false;
        }

        try {
            $response = $this->request($session->auth_key)->post('/create-message', [
                'authkey' => $session->auth_key,
                'appkey' => $session->app_key,
                'to' => $this->normalizeNumber($to),
                'type' => 'text',
                'message' => $body,
            ]);

            if (! $response->successful()) {
                return false;
            }

            // Providers vary: {success:true} or {status:"queued"|"sent"} etc.
            $payload = $response->json() ?? [];
            $success = $payload['success'] ?? $payload['status'] ?? true;

            return ! in_array($success, [false, 'false', 'failed', 'error', 0, '0'], true);
        } catch (\Throwable $e) {
            Log::warning('waapi send failed', [
                'client_id' => $session->client_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function request(?string $authKey): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->acceptJson()
            ->withHeader('authkey', (string) $authKey);
    }

    /**
     * Pull a usable image source out of whatever shape /get-qr returns.
     *
     * @param  array<string, mixed>  $payload
     */
    private function extractQr(array $payload): ?string
    {
        foreach (['qrcode', 'qr', 'base64', 'image', 'data.qrcode', 'data.qr', 'data.base64'] as $key) {
            $value = Arr::get($payload, $key);
            if (filled($value) && is_string($value)) {
                return $this->asImageSource($value);
            }
        }

        return null;
    }

    private function asImageSource(string $value): string
    {
        if (Str::startsWith($value, ['data:', 'http://', 'https://'])) {
            return $value;
        }

        // Bare base64 — wrap it so an <img> can render it directly.
        return 'data:image/png;base64,'.$value;
    }

    private function mapStatus(string $raw): WhatsAppStatus
    {
        $value = Str::lower($raw);

        return match (true) {
            Str::contains($value, ['connect', 'ready', 'auth', 'online', 'active']) => WhatsAppStatus::Connected,
            Str::contains($value, ['qr', 'pending', 'connecting', 'init', 'scan', 'loading']) => WhatsAppStatus::Connecting,
            default => WhatsAppStatus::Disconnected,
        };
    }

    /**
     * waapi expects international format with no leading "+". Defaults a local
     * (leading-zero) number to the Egyptian country code.
     */
    private function normalizeNumber(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if (Str::startsWith($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (Str::startsWith($digits, '0')) {
            $digits = '20'.substr($digits, 1);
        }

        return $digits;
    }
}
