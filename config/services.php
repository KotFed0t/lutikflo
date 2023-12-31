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

    'voicePassword' => [
        'url' => env('VOICE_PASSWORD_URL'),
        'apiKey' => env('VOICE_PASSWORD_API_KEY')
    ],

    'tomtom' => [
        'apiKey' => env('TOMTOM_API_KEY')
    ],

    'yookassa' => [
        'apiKey' => env('YOOKASSA_API_KEY'),
        'shopId' => env('YOOKASSA_SHOP_ID')
    ],

    'robokassa' => [
        'shop_id' => env('ROBOKASSA_SHOP_ID'),
        'pswd_1' => env('ROBOKASSA_PSWD_1'),
        'pswd_2' => env('ROBOKASSA_PSWD_2')
    ],

    'telegram' => [
        'botApiToken' => env('TELEGRAM_BOT_API_TOKEN'),
        'chatId' => env('TELEGRAM_CHAT_ID')
    ]

];
