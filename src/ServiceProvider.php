<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Contracts\Http\Kernel as KernelContract;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services and middleware.
     *
     * @param KernelContract $kernel
     *
     * @return void
     */
    public function boot(KernelContract $kernel): void
    {
        if ($kernel instanceof Kernel) {
            // NOTE: Registering order is very important
            // ForceHttpsMiddleware MUST be registered EARLIER then SetServerPortMiddleware (for registering used
            // method "prependMiddleware", so, each register method push middleware to the TOP of middleware set)
            $this->registerSetServerPortMiddleware($kernel);
            $this->registerForceHttpsMiddleware($kernel);
        }
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
     * @param Kernel $kernel
     *
     * @return void
     */
    protected function registerForceHttpsMiddleware(Kernel $kernel): void
    {
        $kernel->prependMiddleware(Middleware\ForceHttpsMiddleware::class);
    }

    /**
     * Register "SetServerPortMiddleware".
     *
     * @param Kernel $kernel
     *
     * @return void
     */
    protected function registerSetServerPortMiddleware(Kernel $kernel): void
    {
        $kernel->prependMiddleware(Middleware\SetServerPortMiddleware::class);
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
