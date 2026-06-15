<?php

namespace App\Providers;

use App\Contracts\MessageGateway;
use App\Services\Messaging\LogMessageGateway;
use App\Services\WhatsApp\WaapiClient;
use App\Services\WhatsApp\WhatsAppGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WaapiClient::class, fn (): WaapiClient => new WaapiClient(
            baseUrl: (string) config('whatsapp.waapi.base_url'),
            timeout: (int) config('whatsapp.waapi.timeout', 15),
        ));

        // The reminder pipeline talks only to the MessageGateway contract; the
        // concrete transport is chosen by config so we can swap providers (or
        // move to the official Cloud API later) without touching code.
        $this->app->bind(MessageGateway::class, fn ($app): MessageGateway => match (config('whatsapp.driver')) {
            'waapi' => $app->make(WhatsAppGateway::class),
            default => $app->make(LogMessageGateway::class),
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
