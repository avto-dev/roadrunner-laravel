<?php

namespace AvtoDev\RoadRunnerLaravel;

use Illuminate\Contracts\Container\Container;

interface WorkerInterface
{
    /**
     * Start worker events loop.
     *
     * @return void
     */
    public function start(): void;

    /**
     * @return void
     */
    public function bootstrap(): void;

    /**
     * Set worker container (application) instance.
     *
     * @param Container $container
     *
     * @return void
     */
    public function setContainer(Container $container): void;
}
