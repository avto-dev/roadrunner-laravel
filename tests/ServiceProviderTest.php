<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests;

use AvtoDev\RoadRunnerLaravel\Middleware\SetServerPortMiddleware;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use AvtoDev\RoadRunnerLaravel\Middleware\ForceHttpsMiddleware;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\ServiceProvider
 */
class ServiceProviderTest extends AbstractTestCase
{
    /**
     * @var \Illuminate\Foundation\Http\Kernel
     */
    protected $http_kernel;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->http_kernel = $this->app->make(HttpKernel::class);
    }

    /**
     * @return void
     */
    public function testForceHttpsMiddlewareRegistered(): void
    {
        $this->assertTrue($this->http_kernel->hasMiddleware(ForceHttpsMiddleware::class));
    }

    /**
     * @return void
     */
    public function testSetServerPortMiddlewareRegistered(): void
    {
        $this->assertTrue($this->http_kernel->hasMiddleware(SetServerPortMiddleware::class));
    }

    /**
     * @small
     *
     * @return void
     */
    public function testMiddlewareOrder(): void
    {
        /** @var string[] $middleware */
        $middleware = $this->getObjectAttribute($this->http_kernel, 'middleware');

        $force_https_index = \array_search(ForceHttpsMiddleware::class, $middleware);
        $set_port_index = \array_search(SetServerPortMiddleware::class, $middleware);

        $this->assertNotEquals($force_https_index, $set_port_index);

        $this->assertTrue(
            $force_https_index < $set_port_index,
            'ForceHttpsMiddleware MUST be registered EARLIER then SetServerPortMiddleware'
        );
    }
}
