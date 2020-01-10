<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Resetters;

use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class ClearInstances implements ResetterInterface
{
    /**
     * @param AfterLoopIterationEvent $event
     *
     * {@inheritDoc}
     */
    public function handle($event): void
    {
        $container = $event->worker->getContainer();

        /** @var ConfigRepository $config */
        $config = $container->make(ConfigRepository::class);

        foreach ((array) $config->get(ServiceProvider::getConfigRootKeyName() . '.instances', []) as $abstract) {
            $container->forgetInstance($abstract);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function listenForEvents(): array
    {
        return [
            AfterLoopIterationEvent::class,
        ];
    }
}
