<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads and stores a center logo', function () {
    Storage::fake('public');
    [$client, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->put(route('tenant.settings.update'), [
        'currency' => 'EGP',
        'timezone' => 'Africa/Cairo',
        'logo' => UploadedFile::fake()->image('logo.png'),
    ])->assertRedirect(route('tenant.settings.edit'));

    $path = $client->fresh()->logo_path;
    expect($path)->not->toBeNull();
    Storage::disk('public')->assertExists($path);
});

it('rejects a non-image logo', function () {
    Storage::fake('public');
    [, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->put(route('tenant.settings.update'), [
        'currency' => 'EGP',
        'timezone' => 'Africa/Cairo',
        'logo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('logo');
});
