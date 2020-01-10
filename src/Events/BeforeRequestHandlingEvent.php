<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;

final class BeforeRequestHandlingEvent
{
    /**
     * @var WorkerInterface
     */
    public $worker;

    /**
     * @var Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param WorkerInterface $worker
     * @param Request         $request
     */
    public function __construct(WorkerInterface $worker, Request $request)
    {
        $this->worker  = $worker;
        $this->request = $request;
    }
}
