<?php

use App\Models\User;

it('stores a supported locale in the session', function () {
    $this->from(route('login'))
        ->put(route('locale.update', 'ar'))
        ->assertRedirect(route('login'));

    expect(session('locale'))->toBe('ar');
});

it('ignores an unsupported locale', function () {
    $this->put(route('locale.update', 'fr'));

    expect(session('locale'))->toBeNull();
});

it('renders the app in the selected locale', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->withSession(['locale' => 'ar'])
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('dir="rtl"', false);
});
