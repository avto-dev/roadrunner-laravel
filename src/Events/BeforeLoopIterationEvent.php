<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Psr\Http\Message\ServerRequestInterface;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;

final class BeforeLoopIterationEvent
{
    /**
     * @var WorkerInterface
     */
    public $worker;

    /**
     * @var ServerRequestInterface
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param WorkerInterface        $worker
     * @param ServerRequestInterface $request
     */
    public function __construct(WorkerInterface $worker, ServerRequestInterface $request)
    {
        $this->worker  = $worker;
        $this->request = $request;
    }
}
