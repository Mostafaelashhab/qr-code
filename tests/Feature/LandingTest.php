<?php

use App\Models\User;

it('shows the public landing page to guests with plans', function () {
    \App\Models\Plan::factory()->create(['name' => 'Showcase Plan']);

    $this->get('/')
        ->assertOk()
        ->assertSee(__('landing.hero_title'))
        ->assertSee('Showcase Plan')
        ->assertSee(route('login'), false);
});

it('redirects authenticated users to their dashboard', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
});

it('serves an XML sitemap of public pages', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/xml');
    $response->assertSee('<urlset', false)
        ->assertSee(route('register'), false);
});

it('serves a robots.txt pointing to the sitemap', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Disallow: /app/')
        ->assertSee('Sitemap: '.route('sitemap'));
});

it('exposes SEO meta tags and icons on the landing page', function () {
    $this->get('/')
        ->assertSee('<meta name="description"', false)
        ->assertSee('property="og:title"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('rel="icon"', false)
        ->assertSee('rel="manifest"', false)
        ->assertSee('name="theme-color"', false);
});
