<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

it('shows the registration form', function () {
    $this->get(route('register'))->assertOk();
});

it('registers a new center with its admin and logs in', function () {
    $this->post(route('register'), [
        'center_name' => 'New Horizons Center',
        'name' => 'Owner One',
        'email' => 'owner@newhorizons.test',
        'phone' => '0100',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard'));

    $client = Client::firstWhere('name', 'New Horizons Center');
    expect($client)->not->toBeNull()
        ->and($client->slug)->not->toBeEmpty();

    $owner = $client->users()->first();
    expect($owner->email)->toBe('owner@newhorizons.test')
        ->and($owner->role)->toBe(UserRole::ClientAdmin);

    $this->assertAuthenticatedAs($owner);
});

it('validates required fields and unique email', function () {
    User::factory()->clientAdmin()->create(['email' => 'taken@center.test']);

    $this->post(route('register'), [
        'center_name' => '',
        'email' => 'taken@center.test',
        'password' => 'short',
    ])->assertSessionHasErrors(['center_name', 'name', 'email', 'password']);

    $this->assertGuest();
});
