<?php

namespace AvtoDev\RoadRunnerLaravel;

interface WorkerInterface
{
    /**
     * Start worker loop.
     *
     * @return void
     */
    public function start(): void;
}
