<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Features;

use AvtoDev\RoadRunnerLaravel\Worker\Worker;

class CustomWorker extends Worker
{
    private const PATH = __DIR__ . '/worker_works';

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        \file_put_contents(static::PATH, 'Hell yeah!', \LOCK_EX);
    }
}
