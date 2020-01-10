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
     * Get worker container instance.
     *
     * @return Container
     */
    public function getContainer(): Container;

    /**
     * Set worker container (application) instance.
     *
     * @param Container $container
     *
     * @return void
     */
    public function setContainer(Container $container): void;
}
