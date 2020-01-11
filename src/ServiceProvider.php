<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel;

use InvalidArgumentException;
use AvtoDev\RoadRunnerLaravel\Resetters\ResetterInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Get config root key name.
     *
     * @return string roadrunner
     */
    public static function getConfigRootKey(): string
    {
        return \basename(static::getConfigPath(), '.php');
    }

    /**
     * Returns path to the configuration file.
     *
     * @return string
     */
    public static function getConfigPath(): string
    {
        return __DIR__ . '/../config/roadrunner.php';
    }

    /**
     * Register package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->initializeConfigs();
        $this->registerWorker();
    }

    /**
     * Boot package services.
     *
     * @param ConfigRepository $config
     * @param EventsDispatcher $events
     *
     * @return void
     */
    public function boot(ConfigRepository $config, EventsDispatcher $events): void
    {
        $this->bootEventListeners($config, $events);
    }

    /**
     * @param ConfigRepository $config
     * @param EventsDispatcher $events
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function bootEventListeners(ConfigRepository $config, EventsDispatcher $events): void
    {
        foreach ((array) $config->get(static::getConfigRootKey() . '.listeners') as $event => $listeners) {
            foreach (\array_unique($listeners) as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * Register worker.
     *
     * @return void
     */
    protected function registerWorker(): void
    {
        $this->app->bind(WorkerInterface::class, Worker::class);
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs(): void
    {
        $this->mergeConfigFrom(static::getConfigPath(), static::getConfigRootKey());

        $this->publishes([
            \realpath(static::getConfigPath()) => config_path(\basename(static::getConfigPath())),
        ], 'config');
    }
}
