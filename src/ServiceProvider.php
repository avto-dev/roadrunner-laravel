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
     * @return string
     */
    public static function getConfigRootKeyName(): string
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
        $this->bootResetters($config, $events);
    }

    /**
     * @param ConfigRepository $config
     * @param EventsDispatcher $events
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function bootResetters(ConfigRepository $config, EventsDispatcher $events): void
    {
        foreach ((array) $config->get(static::getConfigRootKeyName() . '.resetters') as $resetter_class) {
            if (\is_string($resetter_class) && \class_exists($resetter_class)) {
                if (! isset(\class_implements($resetter_class)[ResetterInterface::class])) {
                    throw new InvalidArgumentException("Wrong resetter class [{$resetter_class}] is set");
                }

                /** @var $resetter_class ResetterInterface */
                $events->listen($resetter_class::listenForEvents(), $resetter_class);
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
        $this->mergeConfigFrom(static::getConfigPath(), static::getConfigRootKeyName());

        $this->publishes([
            \realpath(static::getConfigPath()) => config_path(\basename(static::getConfigPath())),
        ], 'rr-config');
    }
}
