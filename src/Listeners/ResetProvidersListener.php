<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Psr\Http\Message\ServerRequestInterface;

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
            $providers = (array) $config->get(ServiceProvider::getConfigRootKey() . '.providers', []);

            foreach ($providers as $provider) {
                $providerClass = new $provider($app);
                $this->rebindProviderContainer($app, $providerClass);
                if (method_exists($providerClass, 'register')) {
                    $providerClass->register();
                }
                if (method_exists($providerClass, 'boot')) {
                    $app->call([$providerClass, 'boot']);
                }
            }
        }
    }

    /**
     * Rebind service provider's container.
     *
     * @param $app
     * @param $provider
     */
    protected function rebindProviderContainer($app, $provider)
    {
        $closure = function () use ($app) {
            $this->app = $app;
        };
        $resetProvider = $closure->bindTo($provider, $provider);
        $resetProvider();
    }
}
