<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Containers Pre Resolving
    |--------------------------------------------------------------------------
    |
    | Declared here containers will be resolved before events loop will be
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
        'encrypter',
        'hash',
        'router',
        'translator',
        'url',
        'log',
    ],

    'handlers' => [
        AvtoDev\RoadRunnerLaravel\Handlers\ResetAppHandler::class,
        AvtoDev\RoadRunnerLaravel\Handlers\TestDieHandler::class,
    ],
];
