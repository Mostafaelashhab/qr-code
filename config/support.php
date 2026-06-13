<?php

$whatsapp = env('SUPPORT_WHATSAPP', env('SUPPORT_PHONE', '01550047838'));

// Build a wa.me link from an Egyptian local number (0XXXXXXXXXX -> 20XXXXXXXXXX).
$whatsappDigits = preg_replace('/\D/', '', (string) $whatsapp);
if (str_starts_with($whatsappDigits, '0')) {
    $whatsappDigits = '20'.substr($whatsappDigits, 1);
}

return [

    'phone' => env('SUPPORT_PHONE', '01550047838'),

    'whatsapp' => $whatsapp,

    'whatsapp_url' => 'https://wa.me/'.$whatsappDigits,

];
