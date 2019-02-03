<?php

namespace AvtoDev\RoadRunnerLaravel\Worker;

interface WorkerInterface
{
    /**
     * Start worker events loop.
     *
     * @return void
     */
    public function start();
}
