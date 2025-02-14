<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'inter' => [
        'base_url' => env('INTER_API_URL', 'https://cdpj-sandbox.partners.uatinter.co'),
        'oauth_url' => env('INTER_OAUTH_URL', 'https://cdpj-sandbox.partners.uatinter.co/oauth/v2/token'),
        'client_id' => env('INTER_CLIENT_ID'),
        'client_secret' => env('INTER_CLIENT_SECRET'),
        'cert_path' => env('INTER_CERT_PATH'),
        'key_path' => env('INTER_KEY_PATH'),
        'conta_corrente' => env('INTER_CONTA_CORRENTE')
    ],
];
