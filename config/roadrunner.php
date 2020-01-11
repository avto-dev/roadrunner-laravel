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
        //'encrypter', APP_KEY must be set // @todo: uncomment
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
        AvtoDev\RoadRunnerLaravel\Events\BeforeLoopStartedEvent::class => [
            //
        ],

        AvtoDev\RoadRunnerLaravel\Events\BeforeLoopIterationEvent::class => [
            AvtoDev\RoadRunnerLaravel\Listeners\RebindHttpKernelListener::class,
            AvtoDev\RoadRunnerLaravel\Listeners\RebindRouterListener::class,
            AvtoDev\RoadRunnerLaravel\Listeners\RebindViewListener::class,
            AvtoDev\RoadRunnerLaravel\Listeners\CloneConfigListener::class,
            AvtoDev\RoadRunnerLaravel\Listeners\UniqueCookiesListener::class,
            AvtoDev\RoadRunnerLaravel\Listeners\ResetSessionListener::class,
        ],

        AvtoDev\RoadRunnerLaravel\Events\BeforeRequestHandlingEvent::class => [
            AvtoDev\RoadRunnerLaravel\Listeners\BindRequestListener::class,
        ],

        AvtoDev\RoadRunnerLaravel\Events\AfterRequestHandlingEvent::class => [
            //
        ],

        AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent::class => [
            AvtoDev\RoadRunnerLaravel\Listeners\ClearInstancesListener::class,
        ],

        AvtoDev\RoadRunnerLaravel\Events\AfterLoopStoppedEvent::class => [
            //
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
