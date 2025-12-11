<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maya Environment
    |--------------------------------------------------------------------------
    |
    | This value determines which Maya environment your application will use.
    | Supported: "sandbox", "production"
    |
    */

    'environment' => env('MAYA_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Maya API Keys
    |--------------------------------------------------------------------------
    |
    | Your Maya API public and secret keys. You can obtain these from the
    | Maya Developer Hub: https://developers.maya.ph
    |
    */

    'public_key' => env('MAYA_PUBLIC_KEY'),

    'secret_key' => env('MAYA_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Custom Base URLs (Optional)
    |--------------------------------------------------------------------------
    |
    | Override the default Maya API base URLs if needed. Leave null to use
    | the default URLs based on the environment setting.
    |
    */

    'base_url' => env('MAYA_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook handling settings.
    |
    */

    'webhook' => [
        'enabled' => env('MAYA_WEBHOOK_ENABLED', true),
        'route_path' => env('MAYA_WEBHOOK_PATH', 'webhooks/maya'),
        'middleware' => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect URLs
    |--------------------------------------------------------------------------
    |
    | Configure the redirect URLs for payment success, failure, and cancellation.
    | These URLs will be used when creating checkout sessions.
    |
    */

    'redirect_urls' => [
        'success' => env('MAYA_REDIRECT_URL_SUCCESS'),
        'failure' => env('MAYA_REDIRECT_URL_FAILURE'),
        'cancel' => env('MAYA_REDIRECT_URL_CANCEL'),
    ],
];
