<?php

declare(strict_types=1);

namespace AvtoDev\RoadRunnerLaravel;

use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializer;
use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

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
        /**
         * @see CallbacksInitializer::initForceHttps()
         */
        if ($this->app->make(Request::class)->hasHeader(CallbacksInitializerInterface::FORCE_HTTPS_HEADER_NAME)) {
            $this->app->make(UrlGenerator::class)->forceScheme('https');
        }
    }
}
