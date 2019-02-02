<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerWorkerLaravel;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class LaravelPackageServiceProvider extends IlluminateServiceProvider
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
        return __DIR__ . '/config/package-config.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register package services.
     *
     * @return void
     */
    public function register()
    {
        $this->initializeConfigs();
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs()
    {
        $this->mergeConfigFrom(static::getConfigPath(), static::getConfigRootKeyName());

        $this->publishes([
            \realpath(static::getConfigPath()) => config_path(\basename(static::getConfigPath())),
        ], 'config');
    }
}
