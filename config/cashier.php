<?php

use Laravel\Cashier\Invoices\DompdfInvoiceRenderer;

return [

    /*
    |--------------------------------------------------------------------------
    | Mollie Keys
    |--------------------------------------------------------------------------
    |
    | The Mollie API key gives you access to Mollie's API. The "test" key is
    | typically used when testing your application, while the "live" key is
    | used in production.
    |
    */

    'key' => env('MOLLIE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    |
    | The webhook URL that Mollie should call when payment status changes occur.
    | Make sure this URL is accessible from the internet and points to your
    | application's webhook handling route.
    |
    */

    'webhook_url' => env('MOLLIE_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Mollie.
    |
    */

    'currency' => 'EUR',

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => 'nl_NL',

    /*
    |--------------------------------------------------------------------------
    | Trial Days
    |--------------------------------------------------------------------------
    |
    | This is the number of days your customers will get as a free trial when
    | they sign up for your service. If you don't want to offer a trial
    | period, set this value to 0.
    |
    */

    'trial_days' => 14,

    /*
    |--------------------------------------------------------------------------
    | First Payment
    |--------------------------------------------------------------------------
    |
    | These are the settings for the first payment of a subscription. You can
    | specify the payment method, description, and webhook URL. The redirect
    | URL is where the customer will be redirected after the payment.
    |
    */

    'first_payment' => [
        'method' => 'ideal',
        'redirect_url' => env('APP_URL') . '/subscription/callback',
        'webhook_url' => env('MOLLIE_WEBHOOK_URL'),
        'description' => 'First payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mandate
    |--------------------------------------------------------------------------
    |
    | These are the settings for the mandate that will be used for recurring
    | payments. You can specify the method and description for the mandate.
    |
    */

    'mandate' => [
        'method' => 'directdebit',
        'description' => 'Recurring payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where Cashier's views, such as the payment
    | verification screen, will be available from. You're free to tweak
    | this path according to your preferences and application design.
    |
    */

    'path' => env('CASHIER_PATH', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Payment Confirmation Notification
    |--------------------------------------------------------------------------
    |
    | If this setting is enabled, Cashier will automatically notify customers
    | whose payments require additional verification. You should listen to
    | Stripe's webhooks in order for this feature to function correctly.
    |
    */

    'payment_notification' => env('CASHIER_PAYMENT_NOTIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | The following options determine how Cashier invoices are converted from
    | HTML into PDFs. You're free to change the options based on the needs
    | of your application or your preferences regarding invoice styling.
    |
    */

    'invoices' => [
        'renderer' => env('CASHIER_INVOICE_RENDERER', DompdfInvoiceRenderer::class),

        'options' => [
            // Supported: 'letter', 'legal', 'A4'
            'paper' => env('CASHIER_PAPER', 'letter'),

            'remote_enabled' => env('CASHIER_REMOTE_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Logger
    |--------------------------------------------------------------------------
    |
    | This setting defines which logging channel will be used by the Stripe
    | library to write log messages. You are free to specify any of your
    | logging channels listed inside the "logging" configuration file.
    |
    */

    'logger' => env('CASHIER_LOGGER'),

];
