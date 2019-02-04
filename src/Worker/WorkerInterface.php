<?php

namespace AvtoDev\RoadRunnerLaravel\Worker;

interface WorkerInterface
{
    /**
     * Environment name for passing application base path,.
     */
    const ENV_APP_BASE_PATH_NAME = 'APP_BASE_PATH';

    /**
     * Environment name for passing application bootstrap file path,.
     */
    const ENV_APP_BOOTSTRAP_PATH_NAME = 'APP_BOOTSTRAP_PATH';

    /**
     * Start worker events loop.
     *
     * @return void
     */
    public function start();
}
