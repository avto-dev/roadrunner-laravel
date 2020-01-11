<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Containers Pre Resolving
    |--------------------------------------------------------------------------
    |
    | Declared here abstractions will be resolved before events loop will be
    | started.
    |
    */

    'pre_resolving' => [
        'view',
        'files',
        'session',
        'session.store',
        'routes',
        'db',
        'db.factory',
        'cache',
        'cache.store',
        'config',
        'cookie',
        //'encrypter', APP_KEY must be set
        'hash',
        'router',
        'translator',
        'url',
        'log',
    ],

    'resetters' => [
        \AvtoDev\RoadRunnerLaravel\Resetters\ClearInstances::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Instances here will be cleared on every request.
    |--------------------------------------------------------------------------
    */
    'instances' => [
        'auth',
    ],
];
