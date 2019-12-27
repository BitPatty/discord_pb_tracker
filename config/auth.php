<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => true,
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'database',
            'model' => App\Models\User::class,
            'table' => 't_user'
        ],
    ],
];
