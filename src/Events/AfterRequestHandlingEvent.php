<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;
use Symfony\Component\HttpFoundation\Response;

final class AfterRequestHandlingEvent
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
     * @var Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param WorkerInterface $worker
     * @param Request         $request
     * @param Response        $response
     */
    public function __construct(WorkerInterface $worker, Request $request, Response $response)
    {
        $this->worker   = $worker;
        $this->request  = $request;
        $this->response = $response;
    }
}
