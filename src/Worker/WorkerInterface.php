<?php

namespace AvtoDev\RoadRunnerLaravel\Worker;

use AvtoDev\RoadRunnerLaravel\Resetter\ResetAuth;
use AvtoDev\RoadRunnerLaravel\Resetter\ClearCookies;
use AvtoDev\RoadRunnerLaravel\Resetter\ResetRequest;
use AvtoDev\RoadRunnerLaravel\Resetter\ResetSession;

interface WorkerInterface
{
    /**
     * Environment value name for passing application base path.
     */
    public const ENV_NAME_APP_BASE_PATH = 'APP_BASE_PATH';

    /**
     * Environment value name for passing application bootstrap file path.
     */
    public const ENV_NAME_APP_BOOTSTRAP_PATH = 'APP_BOOTSTRAP_PATH';

    /**
     * Environment value name for forcing "https" schema.
     */
    public const ENV_NAME_APP_FORCE_HTTPS = 'APP_FORCE_HTTPS';

    public const APPLICATION_RESETTERS = [
        ClearCookies::class,
        ResetAuth::class,
        ResetRequest::class,
        ResetSession::class,
    ];

    /**
     * Start worker events loop.
     *
     * @return void
     */
    public function start(): void;
}
