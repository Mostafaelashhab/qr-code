<?php

use App\Contracts\MessageGateway;
use App\Enums\MessageChannel;
use App\Models\Client;
use App\Models\WhatsAppSession;
use App\Services\Messaging\LogMessageGateway;
use App\Services\WhatsApp\WhatsAppGateway;
use Illuminate\Support\Facades\Http;

it('resolves the log gateway by default', function () {
    config()->set('whatsapp.driver', 'log');

    expect(app(MessageGateway::class))->toBeInstanceOf(LogMessageGateway::class);
});

it('resolves the WhatsApp gateway when the waapi driver is selected', function () {
    config()->set('whatsapp.driver', 'waapi');

    expect(app(MessageGateway::class))->toBeInstanceOf(WhatsAppGateway::class)
        ->and(app(MessageGateway::class)->channel())->toBe(MessageChannel::WhatsApp);
});

it('sends through waapi using the center credentials and reports success', function () {
    config()->set('whatsapp.driver', 'waapi');
    config()->set('whatsapp.waapi.base_url', 'https://wa.test/api');

    $client = Client::factory()->create();
    WhatsAppSession::withoutGlobalScopes()->create([
        'client_id' => $client->id,
        'auth_key' => 'CENTER-AUTH',
        'device_uuid' => 'dev-1',
        'app_key' => 'APP-KEY',
    ]);

    Http::fake(['wa.test/api/create-message' => Http::response(['success' => true])]);

    expect(app(MessageGateway::class)->send($client, '01001234567', 'Hello'))->toBeTrue();

    Http::assertSent(fn ($request) => str_contains($request->url(), '/create-message')
        && $request['appkey'] === 'APP-KEY'
        && $request['to'] === '201001234567'        // normalized to intl, no +
        && $request->hasHeader('authkey', 'CENTER-AUTH'));
});

it('does not send when the center has no credentials', function () {
    config()->set('whatsapp.driver', 'waapi');
    Http::fake();

    $client = Client::factory()->create();

    expect(app(MessageGateway::class)->send($client, '01001234567', 'Hello'))->toBeFalse();
    Http::assertNothingSent();
});
