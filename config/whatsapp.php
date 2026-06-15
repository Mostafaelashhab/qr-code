<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messaging driver
    |--------------------------------------------------------------------------
    |
    | Which transport delivers reminder messages.
    |
    |   "log"   – writes messages to the log (local/testing, no real send).
    |   "waapi" – sends through the waapi.octopusteam.net provider. Each center
    |             is a "device" on our single waapi account; the super admin
    |             provisions it and the center links its number by scanning a QR.
    |
    | The application code never references a concrete driver; swapping this
    | value (to another provider or the official Cloud API later) requires no
    | changes to controllers or actions.
    |
    */

    'driver' => env('WHATSAPP_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | waapi.octopusteam.net account
    |--------------------------------------------------------------------------
    |
    | Each center is its own waapi account. Its credentials (auth_key, device
    | uuid, app_key) are stored per center and provisioned by the super admin —
    | only the shared base URL and timeout live here.
    |
    */

    'waapi' => [
        'base_url' => rtrim(env('WAAPI_BASE_URL', 'https://waapi.octopusteam.net/api'), '/'),
        'timeout' => (int) env('WAAPI_TIMEOUT', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue + throttling
    |--------------------------------------------------------------------------
    |
    | Reminders are queued (one job per recipient) and spaced out instead of
    | blasted at once — bulk, instant, identical sends are the fastest way to
    | get a WhatsApp number banned. Each recipient is dispatched with a growing
    | delay: a random gap between min_delay and max_delay seconds, never faster
    | than per_minute messages a minute.
    |
    */

    'queue' => env('WHATSAPP_QUEUE', 'whatsapp'),

    'throttle' => [
        'enabled' => env('WHATSAPP_THROTTLE', true),
        'min_delay' => (int) env('WHATSAPP_MIN_DELAY', 4),
        'max_delay' => (int) env('WHATSAPP_MAX_DELAY', 15),
        'per_minute' => (int) env('WHATSAPP_PER_MINUTE', 8),
    ],

];
