<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Spiral\RoadRunner\PSR7Client;
use AvtoDev\RoadRunnerLaravel\Listeners\StopWorkerListener;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\StopWorkerListener<extended>
 */
class StopWorkerListenerTest extends AbstractListenerTestCase
{
    /**
     * @inheritDoc
     */
    protected function listenerFactory()
    {
        return new StopWorkerListener;
    }

    /**
     * @inheritDoc
     */
    public function testHandle(): void
    {
        $worker = m::mock()->shouldReceive('stop')->once()->getMock();

        $psr7_client_mock = new class($worker) {
            protected $worker;

            public function __construct($worker)
            {
                $this->worker = $worker;
            }

            public function getWorker()
            {
                return $this->worker;
            }
        };

        $this->app->instance(PSR7Client::class, $psr7_client_mock);

        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->once()
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);
    }
}
