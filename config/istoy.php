<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of your Order model that implements
    | the OrderContract interface. This model should have:
    | - update() method
    | - scopeWithExternalId() scope
    | - external_id, service, status, start_count, remains fields
    |
    */
    'order_model' => env('ISTOY_ORDER_MODEL', \App\Models\Order::class),

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each service provider. You can add multiple providers
    | here and register them using the Factory::register() method.
    |
    */
    'providers' => [
        'smm' => [
            'host' => env('SMM_PROVIDER_HOST', 'https://smmlite.com/api/v2'),
            'key' => env('SMM_PROVIDER_KEY', ''),
        ],
    ],
];

