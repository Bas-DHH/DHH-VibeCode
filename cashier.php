<?php

return [
    'mollie' => [
        'key' => env('MOLLIE_KEY'),
        'webhook_url' => env('MOLLIE_WEBHOOK_URL'),
    ],

    'currency' => 'EUR',
    'currency_locale' => 'nl_NL',
    'trial_days' => 14,
]; 