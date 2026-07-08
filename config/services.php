<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'daily' => [
        'key' => env('DAILY_API_KEY'),
    ],

    'mirth' => [
        'base_url' => env('MIRTH_BASE_URL', 'https://mirth:8443'),
        'api_key' => env('MIRTH_API_KEY'),
        'channel_id' => env('MIRTH_CHANNEL_ID'),
    ],

    'hie' => [
        'endpoint' => env('HIE_ENDPOINT', 'https://hie.example.com/fhir'),
        'api_key' => env('HIE_API_KEY'),
        'org_id' => env('HIE_ORG_ID'),
    ],

    'orthanc' => [
        'url' => env('ORTHANC_URL', 'https://orthanc:8042'),
        'username' => env('ORTHANC_USERNAME'),
        'password' => env('ORTHANC_PASSWORD'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'paystack' => [
        'public' => env('PAYSTACK_PUBLIC_KEY'),
        'secret' => env('PAYSTACK_SECRET_KEY'),
    ],

    'anthropic' => [
        'secret' => env('ANTHROPIC_API_KEY'),
        'mock' => env('ANTHROPIC_MOCK', true),
    ],

];
