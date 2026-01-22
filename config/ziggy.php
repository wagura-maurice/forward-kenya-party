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
        'web' => [
            'frontend.*',
            'dashboard',
            'profile.*',
            'activity.*',
            'settings',
            'verification.*',
            'frontend.terms-and-conditions',
            'frontend.privacy-policy',
            'frontend.help-and-support',
            'frontend.frequently-asked-questions',
            'frontend.services',
            'frontend.show.service',
            'frontend.departments',
            'frontend.show.department',
        ],
        'export' => [
            'export.*',
        ],
        'auth' => [
            'auth.*',
            'verification.*',
        ],
        'api' => [
            'api.*',
            'light.of.guidances.*',
            'setting.*',
            'receipt.*',
            'notifications.*',
        ],
    ],

    // Exclude any routes that shouldn't be exposed to the frontend
    'except' => [
        'passport.*',
        'ignition.*',
        'sanctum.*',
        'horizon.*',
        'telescope.*',
    ],

    // If you want to explicitly include only certain routes, uncomment and modify this
    // 'only' => [
    //     'frontend.*',
    //     'dashboard',
    //     'profile.*',
    //     'auth.*',
    //     'verification.*',
    // ],

    'url' => env('APP_URL', 'http://localhost'),
];