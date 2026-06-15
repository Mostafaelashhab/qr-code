<?php

return [
    'nav' => 'WhatsApp',
    'title' => 'WhatsApp connection',
    'intro' => 'Link your center\'s WhatsApp number to send attendance and payment reminders from it. Scan the QR code with the phone that holds the number.',

    'connection_status' => 'Connection status',
    'status' => [
        'disconnected' => 'Not connected',
        'connecting' => 'Waiting for scan',
        'connected' => 'Connected',
    ],

    // Center-facing states
    'preparing_title' => 'Your WhatsApp is being prepared',
    'preparing_hint' => 'We are setting up your WhatsApp account. The QR code will appear here shortly — please check back soon.',
    'connected_title' => 'WhatsApp is connected',
    'connected_hint' => 'Reminders will be sent from your linked number.',
    'waiting_hint' => 'Preparing the QR code…',
    'scan_hint' => 'Open WhatsApp on your phone and scan this code.',

    'step_1' => 'Open WhatsApp on the phone that owns the number.',
    'step_2' => 'Go to Settings → Linked devices → Link a device.',
    'step_3' => 'Scan the QR code shown above.',

    // Super-admin provisioning
    'provisioning_title' => 'WhatsApp (waapi)',
    'provisioning_hint' => 'Create this center\'s account/device in the waapi dashboard, then paste its credentials here so the center can link its number.',
    'provisioning_saved' => 'WhatsApp credentials saved.',
    'auth_key' => 'Auth key',
    'device_uuid' => 'Device UUID',
    'app_key' => 'App key',
];
