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

    'ticktick' => [
        'token' => env('TICKTICK_TOKEN'),
        'username' => env('TICKTICK_USERNAME'),
        'password' => env('TICKTICK_PASSWORD'),
        'base_url' => env('TICKTICK_BASE_URL', 'https://ticktick.com'),
    ],
    'pocketid' => [
        'base_url' => env('POCKETID_URL'),
        'client_id' => env('POCKETID_CLIENT_ID'),
        'client_secret' => env('POCKETID_CLIENT_SECRET'),
        'redirect' => env('POCKETID_REDIRECT_URI'),
    ],
];
