<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\Listeners\BindRequestListener;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\BindRequestListener<extended>
 */
class BindRequestListenerTest extends AbstractListenerTestCase
{
    /**
     * @return BindRequestListener
     */
    protected function listenerFactory(): BindRequestListener
    {
        return new BindRequestListener;
    }

    /**
     * {@inheritDoc}
     */
    public function testHandle(): void
    {
        /** @var Request $modified_request */
        $modified_request = clone $this->app->make('request');

        $event_mock = m::mock(\implode(',', [WithApplication::class, WithHttpRequest::class]))
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock()
            ->expects('httpRequest')
            ->andReturn($modified_request)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($modified_request, $this->app->make('request'));
    }
}
