<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use AvtoDev\RoadRunnerLaravel\Middleware\ForceHttpsMiddleware;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\ServiceProvider
 */
class ServiceProviderTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testForceHttpsMiddlewareRegistered(): void
    {
        $this->assertTrue($this->app->make(HttpKernel::class)->hasMiddleware(ForceHttpsMiddleware::class));
    }
}
