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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'africas_talking' => [
        'username' => env('AFRICAS_TALKING_USERNAME'),
        'api_key' => env('AFRICAS_TALKING_API_KEY'),
        'sender_id' => env('AFRICAS_TALKING_SENDER_ID'),
        'messaging_endpoint' => env('AFRICAS_TALKING_MESSAGING_ENDPOINT'),
        'user_endpoint' => env('AFRICAS_TALKING_USER_ENDPOINT'),
        'balance' => env('AFRICAS_TALKING_BALANCE'),
    ],

    'mpesa' => [
        'lnmo_consumer_key' => env('MPESA_LNMO_CONSUMER_KEY'),
        'lnmo_consumer_secret' => env('MPESA_LNMO_CONSUMER_SECRET'),
        'lnmo_environment' => env('MPESA_LNMO_ENVIRONMENT'),
        'lnmo_initiator_password' => env('MPESA_LNMO_INITIATOR_PASSWORD'),
        'lnmo_initiator_username' => env('MPESA_LNMO_INITIATOR_USERNAME'),
        'lnmo_pass_key' => env('MPESA_LNMO_PASS_KEY'),
        'lnmo_short_code' => env('MPESA_LNMO_SHORT_CODE'),
    ],

    'waha' => [
        'api_url' => env('WAHA_API_URL'),
        'api_key' => env('WAHA_API_KEY'),
        'session' => env('WAHA_SESSION'),
    ],

    'ippms' => [
        'base_url' => env('IPPMS_BASE_URL'),
        'username' => env('IPPMS_USERNAME'),
        'password' => env('IPPMS_PASSWORD'),
    ],

    'recaptcha' => [
        'enabled' => env('RECAPTCHA_ENABLED'),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'skip_ip' => env('RECAPTCHA_SKIP_IP'),
    ],

];
