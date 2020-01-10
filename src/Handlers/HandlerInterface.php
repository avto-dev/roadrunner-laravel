<?php

namespace AvtoDev\RoadRunnerLaravel\Handlers;

use Illuminate\Contracts\Container\Container;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;

interface HandlerInterface
{
    /**
     * @param Container       $container
     * @param WorkerInterface $worker
     *
     * @return mixed
     */
    public function handle(Container $container, WorkerInterface $worker);
}
