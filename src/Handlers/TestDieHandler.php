<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;

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
