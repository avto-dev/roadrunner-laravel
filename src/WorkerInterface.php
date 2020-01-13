<?php

namespace AvtoDev\RoadRunnerLaravel;

interface WorkerInterface
{
    /**
     * Start worker loop.
     *
     * @param bool $refresh_app
     *
     * @return void
     */
    public function start(bool $refresh_app = false): void;
}
