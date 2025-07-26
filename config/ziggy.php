<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ziggy Configuration
    |--------------------------------------------------------------------------
    |
    | This file is used to configure Ziggy, the package that allows you to use
    | your Laravel named routes in JavaScript. You can specify which routes
    | should be included in the Ziggy route list.
    |
    */

    'groups' => [
        'default' => [
            'login', 'register', 'logout', 'dashboard', 'otp.send', 'otp.verify',
        ],
    ],

    'except' => [
        // 
    ],

    'only' => [
        // 
    ],

    'url' => env('APP_URL', 'http://localhost'),
];