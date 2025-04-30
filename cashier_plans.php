<?php

use Money\Money;

return [
    'plans' => [
        'basic' => [
            'amount' => Money::EUR(1000), // €10.00
            'interval' => 'month',
            'description' => 'Basic Plan',
        ],
        'pro' => [
            'amount' => Money::EUR(2000), // €20.00
            'interval' => 'month',
            'description' => 'Pro Plan',
        ],
        'enterprise' => [
            'amount' => Money::EUR(5000), // €50.00
            'interval' => 'month',
            'description' => 'Enterprise Plan',
        ],
    ],

    'defaults' => [
        'order_item_preprocessors' => [
            \Mollie\Laravel\Cashier\Order\PreprocessOrderItems\PreprocessOrderItems::class,
        ],
    ],
]; 