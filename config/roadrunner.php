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

    /*
    |--------------------------------------------------------------------------
    | Event Listeners
    |--------------------------------------------------------------------------
    |
    | Worker provided by this package allows to interacts with request
    | processing loop using application events. Feel free to add your own event
    | listeners.
    |
    */

    'listeners' => [
        AvtoDev\RoadRunnerLaravel\Events\BeforeRequestHandlingEvent::class => [
            AvtoDev\RoadRunnerLaravel\Listeners\BindRequestListener::class,
        ],

        AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent::class => [
            AvtoDev\RoadRunnerLaravel\Listeners\ClearInstancesListener::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Instances Clearing
    |--------------------------------------------------------------------------
    |
    | Instances described here will be cleared on every request (if
    | `ClearInstancesListener` is enabled).
    |
    */
    'clear_instances' => [
        'auth',
    ],
];
