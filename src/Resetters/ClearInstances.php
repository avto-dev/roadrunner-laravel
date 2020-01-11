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
     * @return void
     */
    public function handle($event): void
    {
        if ($event->app instanceof \Illuminate\Container\Container) {
            /** @var ConfigRepository $config */
            $config = $event->app->make(ConfigRepository::class);

            foreach ((array) $config->get(ServiceProvider::getConfigRootKey() . '.instances', []) as $abstract) {
                $event->app->forgetInstance($abstract);
            }
        }
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
