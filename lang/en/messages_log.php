<?php

return [
    'type' => [
        'general' => 'General',
        'absence' => 'Absence',
        'payment_due' => 'Payment due',
    ],

    'channel_label' => 'Channel',
    'channel' => [
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

    'absence_body' => ':name was absent from :group on :date.',
    'payment_due_body' => 'Reminder: :amount is due for :name in :group for :month.',
];
