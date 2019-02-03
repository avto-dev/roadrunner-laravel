<?php

declare(strict_types=1);

namespace AvtoDev\RoadRunnerLaravel;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializerInterface;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register service and listener.
     *
     * @return void
     */
    public function boot()
    {
        /** @see \AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializer::initForceHttps() */
        if ($this->app->make(Request::class)->hasHeader(CallbacksInitializerInterface::FORCE_HTTPS_HEADER_NAME)) {
            $this->app->make(UrlGenerator::class)->forceScheme('https');
        }
    }
}
