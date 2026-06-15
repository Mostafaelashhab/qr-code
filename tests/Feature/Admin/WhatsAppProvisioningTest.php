<?php

use App\Models\Client;
use App\Models\User;
use App\Models\WhatsAppSession;

it('lets a super admin provision a center WhatsApp device', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $client = Client::factory()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.clients.whatsapp.update', $client), [
            'auth_key' => 'auth-abc',
            'device_uuid' => 'dev-abc',
            'app_key' => 'app-abc',
        ])
        ->assertRedirect(route('admin.clients.show', $client));

    $session = WhatsAppSession::withoutGlobalScopes()->where('client_id', $client->id)->first();
    expect($session)->not->toBeNull()
        ->and($session->auth_key)->toBe('auth-abc')
        ->and($session->device_uuid)->toBe('dev-abc')
        ->and($session->app_key)->toBe('app-abc')
        ->and($session->isProvisioned())->toBeTrue();
});

it('forbids a tenant admin from provisioning devices', function () {
    [, $admin] = tenantWithAdmin();
    $client = Client::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.clients.whatsapp.update', $client), ['device_uuid' => 'x'])
        ->assertForbidden();
});
