<?php

use App\Models\User;

it('lets a center admin update settings', function () {
    [$client, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->put(route('tenant.settings.update'), [
        'currency' => 'USD',
        'timezone' => 'UTC',
        'default_monthly_fee' => 350,
        'phone' => '0100',
    ])->assertRedirect(route('tenant.settings.edit'));

    expect($client->fresh())
        ->currency->toBe('USD')
        ->timezone->toBe('UTC')
        ->and((float) $client->fresh()->default_monthly_fee)->toBe(350.0);
});

it('rejects an invalid timezone', function () {
    [, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->put(route('tenant.settings.update'), [
        'currency' => 'EGP',
        'timezone' => 'Not/AZone',
    ])->assertSessionHasErrors('timezone');
});

it('forbids regular staff from settings', function () {
    [$client] = tenantWithAdmin();
    $staff = User::factory()->clientUser($client)->create();

    $this->actingAs($staff)->get(route('tenant.settings.edit'))->assertForbidden();
});
