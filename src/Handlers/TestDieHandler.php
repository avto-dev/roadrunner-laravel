<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Handlers;

use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Just for a test
 */
class TestDieHandler implements HandlerInterface, RunBeforeLoopContract
{
    public function handle(WorkerInterface $worker, ?Request $request = null, ?Response $response = null)
    {
        dd($worker, $request, $response);
    }
}
