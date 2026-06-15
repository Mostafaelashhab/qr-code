<?php

use App\Enums\WhatsAppStatus;
use App\Models\User;
use App\Models\WhatsAppSession;
use Illuminate\Support\Facades\Http;

it('shows the connection page to a center admin', function () {
    [, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->get(route('tenant.whatsapp.show'))
        ->assertOk()
        ->assertSee(__('whatsapp.title'));
});

it('forbids non-admin staff from the connection page', function () {
    [$client] = tenantWithAdmin();
    $staff = User::factory()->clientUser($client)->create();

    $this->actingAs($staff)->get(route('tenant.whatsapp.show'))->assertForbidden();
});

it('reports "not provisioned" until the super admin sets up a device', function () {
    [, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->getJson(route('tenant.whatsapp.qr'))
        ->assertOk()
        ->assertJson(['provisioned' => false, 'connected' => false, 'qr' => null]);
});

it('returns the QR while a provisioned device waits to link', function () {
    [$client, $admin] = tenantWithAdmin();
    WhatsAppSession::withoutGlobalScopes()->create([
        'client_id' => $client->id,
        'auth_key' => 'auth-123',
        'device_uuid' => 'dev-123',
        'app_key' => 'app-123',
    ]);

    Http::fake([
        '*/get-status' => Http::response(['status' => 'pending']),
        '*/get-qr*' => Http::response(['qrcode' => 'data:image/png;base64,AAAA']),
    ]);

    $this->actingAs($admin)->getJson(route('tenant.whatsapp.qr'))
        ->assertOk()
        ->assertJson([
            'provisioned' => true,
            'connected' => false,
            'qr' => 'data:image/png;base64,AAAA',
        ]);
});

it('persists a connected device reported by waapi', function () {
    [$client, $admin] = tenantWithAdmin();
    WhatsAppSession::withoutGlobalScopes()->create([
        'client_id' => $client->id,
        'auth_key' => 'auth-123',
        'device_uuid' => 'dev-123',
        'app_key' => 'app-123',
    ]);

    Http::fake(['*/get-status' => Http::response(['status' => 'connected'])]);

    $this->actingAs($admin)->getJson(route('tenant.whatsapp.qr'))
        ->assertOk()
        ->assertJson(['provisioned' => true, 'connected' => true, 'qr' => null]);

    $session = WhatsAppSession::withoutGlobalScopes()->where('client_id', $client->id)->first();
    expect($session->status)->toBe(WhatsAppStatus::Connected)
        ->and($session->last_connected_at)->not->toBeNull();
});
