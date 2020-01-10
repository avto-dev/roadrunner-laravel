<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Handlers;

use RuntimeException;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\WorkerInterface;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;

class ResetAppHandler implements HandlerInterface, RunAfterLoopIterationContract
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Create a new handler instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Container $container
     *
     * @throws RuntimeException If bootstrap file was not found
     *
     * @return Application
     */
    public function createApplication(Container $container): Application
    {
        $path = $container->bootstrapPath('app.php');

        if (! \is_file($path)) {
            throw new RuntimeException("Application bootstrap file [$path] was not found");
        }

        return require $path;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(WorkerInterface $worker, ?Request $request = null, ?Response $response = null)
    {
        $worker->setContainer($this->createApplication($this->container));
        $worker->bootstrap();
    }
}
