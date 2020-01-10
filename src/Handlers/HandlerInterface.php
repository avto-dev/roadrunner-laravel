<?php

namespace AvtoDev\RoadRunnerLaravel\Handlers;

use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;
use Symfony\Component\HttpFoundation\Response;

interface HandlerInterface
{
    /**
     * @param WorkerInterface $worker
     * @param Request|null    $request
     * @param Response|null   $response
     *
     * @return mixed
     */
    public function handle(WorkerInterface $worker, ?Request $request = null, ?Response $response = null);
}
