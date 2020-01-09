<?php

namespace AvtoDev\RoadRunnerLaravel;

interface WorkerInterface
{
    /**
     * Start worker events loop.
     *
     * @return void
     */
    public function start(): void;
}
