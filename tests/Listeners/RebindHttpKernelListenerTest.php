<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Listeners\RebindHttpKernelListener;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\RebindHttpKernelListener<extended>
 */
class RebindHttpKernelListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $fake_kernel = new class {
            /** @var ApplicationContract|null */
            private $app;

            public function _getApp(): ?ApplicationContract
            {
                return $this->app;
            }
        };

        $this->app->instance(HttpKernel::class, $fake_kernel);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->assertNull($this->app->make(HttpKernel::class)->_getApp());

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->app->make(HttpKernel::class)->_getApp());
    }

    /**
     * @return RebindHttpKernelListener
     */
    protected function listenerFactory(): RebindHttpKernelListener
    {
        return new RebindHttpKernelListener;
    }
}
