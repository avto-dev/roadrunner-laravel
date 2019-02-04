<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests;

use AvtoDev\RoadRunnerLaravel\Middleware\ForceHttpsMiddleware;
use Illuminate\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

class ServiceProviderTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testForceHttpsMiddlewareRegistered()
    {
        $this->assertTrue($this->app->make(HttpKernel::class)->hasMiddleware(ForceHttpsMiddleware::class));
    }

    /**
     * @return void
     */
    protected function afterApplicationBootstrapped(Application $app)
    {
        $app->register(ServiceProvider::class);
    }
}
