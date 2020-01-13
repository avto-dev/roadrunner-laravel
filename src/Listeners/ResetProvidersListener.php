<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/ResetProviders.php
 */
class ResetProvidersListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            /** @var ConfigRepository $config */
            $config    = $app->make(ConfigRepository::class);
            $providers = (array) $config->get(ServiceProvider::getConfigRootKey() . '.reset_providers', []);

            foreach (\array_unique($providers) as $provider_class) {
                /** @var \Illuminate\Support\ServiceProvider $provider */
                $provider = new $provider_class($app);

                $this->rebindProviderContainer($app, $provider);

                if (\method_exists($provider, $register_method = 'register')) {
                    $provider->{$register_method}();
                }

                if (\method_exists($provider, $boot_method = 'boot')) {
                    $app->call([$provider, $boot_method]);
                }
            }
        }
    }

    /**
     * Rebind service provider's container.
     *
     * @param ApplicationContract $app
     * @param object              $provider
     *
     * @return void
     */
    protected function rebindProviderContainer(ApplicationContract $app, $provider): void
    {
        $closure = function () use ($app) {
            $this->{'app'} = $app;
        };

        $reseter = $closure->bindTo($provider, $provider);
        $reseter();
    }
}
