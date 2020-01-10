<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Resetters;

use RuntimeException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent;

class ResetApplication implements ResetterInterface
{
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
     * @param AfterLoopIterationEvent $event
     *
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        $event->worker->setContainer($this->createApplication($event->worker->getContainer()));
        $event->worker->bootstrap();
    }

    /**
     * {@inheritdoc}
     */
    public static function listenForEvents(): array
    {
        return [
            AfterLoopIterationEvent::class,
        ];
    }
}
