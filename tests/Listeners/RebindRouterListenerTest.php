<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use AvtoDev\RoadRunnerLaravel\Listeners\RebindRouterListener;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\RebindRouterListener<extended>
 */
class RebindRouterListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;
        /** @var Request $request */
        $request = $this->app->make('request');
        /** @var Router $router */
        $router = $this->app->make('router');

        $this->setProperty($router, $container_prop = 'container', $app_clone);
        $this->setProperty($router->getRoutes()->match($request), $container_prop, $app_clone);

        $this->assertSame($app_clone, $this->getProperty($router, $container_prop));
        $this->assertSame($app_clone, $this->getProperty($router->getRoutes()->match($request), $container_prop));

        /** @var m\MockInterface|WithApplication|WithHttpRequest $event_mock */
        $event_mock = m::mock(\implode(',', [WithApplication::class, WithHttpRequest::class]))
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock()
            ->expects('httpRequest')
            ->andReturn($request)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($router, $container_prop));
        $this->assertSame($this->app, $this->getProperty($router->getRoutes()->match($request), $container_prop));
    }

    /**
     * @return RebindRouterListener
     */
    protected function listenerFactory(): RebindRouterListener
    {
        return new RebindRouterListener;
    }
}
