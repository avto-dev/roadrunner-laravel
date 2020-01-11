<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use Illuminate\Container\Container;
use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

class ClearInstancesListener implements ListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            if ($app instanceof Container) {
                /** @var ConfigRepository $config */
                $config    = $app->make(ConfigRepository::class);
                $abstracts = (array) $config->get(ServiceProvider::getConfigRootKey() . '.clear_instances', []);

                foreach ($abstracts as $abstract) {
                    $app->forgetInstance($abstract);
                }
            }
        }
    }
}
