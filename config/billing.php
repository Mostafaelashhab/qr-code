<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Payment Channels
    |--------------------------------------------------------------------------
    |
    | Centers pay their platform subscription by transferring to one of these
    | accounts (InstaPay or Vodafone Cash), then submit the transfer reference
    | for a super admin to review and approve.
    |
    */

    'instapay_address' => env('BILLING_INSTAPAY_ADDRESS', 'platform@instapay'),

    'vodafone_cash_number' => env('BILLING_VODAFONE_CASH', '01000000000'),

    /*
    |--------------------------------------------------------------------------
    | Trial & expiry
    |--------------------------------------------------------------------------
    |
    | New centers get a free trial on the cheapest active plan. The warning
    | window controls when the "subscription expiring soon" banner appears.
    |
    */

    'trial_days' => (int) env('BILLING_TRIAL_DAYS', 14),

    'expiry_warning_days' => (int) env('BILLING_EXPIRY_WARNING_DAYS', 7),

];

