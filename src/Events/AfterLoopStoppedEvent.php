<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use AvtoDev\RoadRunnerLaravel\WorkerInterface;

final class AfterLoopStoppedEvent
{
    /**
     * @var WorkerInterface
     */
    public $worker;

    /**
     * Create a new event instance.
     *
     * @param WorkerInterface $worker
     */
    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }
}
