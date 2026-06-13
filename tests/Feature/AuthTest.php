<?php

use App\Models\User;

it('shows the login screen', function () {
    $this->get(route('login'))->assertOk()->assertSee('login', false);
});

it('lets an active user log in', function () {
    $user = User::factory()->superAdmin()->create([
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->superAdmin()->create(['password' => 'password']);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects a deactivated user', function () {
    $user = User::factory()->clientAdmin()->create([
        'password' => 'password',
        'is_active' => false,
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs the user out', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

    $this->assertGuest();
});
