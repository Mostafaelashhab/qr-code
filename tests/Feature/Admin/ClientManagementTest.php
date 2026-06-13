<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
});

it('creates a client together with its admin user', function () {
    $this->actingAs($this->admin)->post(route('admin.clients.store'), [
        'name' => 'Bright Future Center',
        'email' => 'info@bright.test',
        'is_active' => true,
        'owner_name' => 'Owner One',
        'owner_email' => 'owner@bright.test',
        'owner_password' => 'password',
        'owner_password_confirmation' => 'password',
    ])->assertRedirect();

    $client = Client::firstWhere('name', 'Bright Future Center');
    expect($client)->not->toBeNull()
        ->and($client->slug)->not->toBeEmpty();

    $owner = $client->users()->first();
    expect($owner->email)->toBe('owner@bright.test')
        ->and($owner->role)->toBe(UserRole::ClientAdmin);
});

it('validates required fields when creating a client', function () {
    $this->actingAs($this->admin)->post(route('admin.clients.store'), [])
        ->assertSessionHasErrors(['name', 'owner_name', 'owner_email', 'owner_password']);
});

it('updates a client', function () {
    $client = Client::factory()->create();

    $this->actingAs($this->admin)->put(route('admin.clients.update', $client), [
        'name' => 'Renamed Center',
        'is_active' => false,
    ])->assertRedirect(route('admin.clients.show', $client));

    expect($client->fresh())
        ->name->toBe('Renamed Center')
        ->is_active->toBeFalse();
});

it('prevents a tenant user from creating clients', function () {
    $user = User::factory()->clientAdmin()->create();

    $this->actingAs($user)->post(route('admin.clients.store'), [])->assertForbidden();
});
