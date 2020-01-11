<?php

use AvtoDev\RoadRunnerLaravel\Events;
use AvtoDev\RoadRunnerLaravel\Listeners;

return [

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS Schema Usage
    |--------------------------------------------------------------------------
    |
    | Set this value to `true` if your application uses HTTPS (required for
    | example for correct links generation).
    |
    */

    'force_https' => (bool) env('APP_FORCE_HTTPS', true),

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
        Events\BeforeLoopStartedEvent::class => [
            Listeners\FixSymfonyFileValidationListener::class,
        ],

        Events\BeforeLoopIterationEvent::class => [
            Listeners\RebindHttpKernelListener::class,
            Listeners\RebindRouterListener::class,
            Listeners\RebindViewListener::class,
            Listeners\CloneConfigListener::class,
            Listeners\UniqueCookiesListener::class,
            Listeners\ResetSessionListener::class,
        ],

        Events\BeforeRequestHandlingEvent::class => [
            Listeners\InjectStatsIntoRequestListener::class,
            Listeners\BindRequestListener::class,
            Listeners\SetServerPortListener::class,
            Listeners\ForceHttpsListener::class,
        ],

        Events\AfterRequestHandlingEvent::class => [
            //
        ],

        Events\AfterLoopIterationEvent::class => [
            Listeners\ClearInstancesListener::class,
            Listeners\ResetDbConnectionsListener::class,
            Listeners\RunGarbageCollectionListener::class,
        ],

        Events\AfterLoopStoppedEvent::class => [
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
