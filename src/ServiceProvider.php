<?php

declare(strict_types=1);

namespace AvtoDev\RoadRunnerLaravel;

use Illuminate\Contracts\Http\Kernel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register services and middleware.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make(Kernel::class)->pushMiddleware(Middleware\ForceHttpsMiddleware::class);
    }
}
