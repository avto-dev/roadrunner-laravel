<?php

declare(strict_types=1);

namespace AvtoDev\RoadRunnerLaravel;

use Illuminate\Contracts\Http\Kernel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services and middleware.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerForceHttpsMiddleware();
    }

    /**
     * Register services and etc.
     *
     * @return void
     */
    public function register(): void
    {
        $this->initializePublishes();
    }

    /**
     * Register "ForceHttpsMiddleware".
     *
     * @return void
     */
    protected function registerForceHttpsMiddleware(): void
    {
        $this->app->make(Kernel::class)->pushMiddleware(Middleware\ForceHttpsMiddleware::class);
    }

    /**
     * Initialize publishes.
     *
     * @return void
     */
    protected function initializePublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../configs/rr' => $this->app->basePath(),
        ], 'rr-config');
    }
}
