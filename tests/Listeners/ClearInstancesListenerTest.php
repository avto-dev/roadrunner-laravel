<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Listeners\ClearInstancesListener;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\ClearInstancesListener<extended>
 */
class ClearInstancesListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @var ConfigRepository $config */
        $config = $this->app->make(ConfigRepository::class);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        // Define custom container abstracts
        $abstracts = ['foo', 'bar'];

        // Make instances ("bind") in container
        foreach ($abstracts as $abstract) {
            $this->app->instance($abstract, $abstract . '-for-test');
        }

        // Assert that instances are presents in container
        foreach ($abstracts as $abstract) {
            $this->assertTrue($this->app->bound($abstract));
            $this->assertSame($abstract . '-for-test', $this->app->make($abstract));
        }

        // Set config value for instances clearing
        $config->set('roadrunner.clear_instances', $abstracts);

        $this->listenerFactory()->handle($event_mock);

        // Assert that instances are not presents in container after listener handling
        foreach ($abstracts as $abstract) {
            $this->assertFalse($this->app->bound($abstract));
        }
    }

    /**
     * @return ClearInstancesListener
     */
    protected function listenerFactory(): ClearInstancesListener
    {
        return new ClearInstancesListener;
    }
}
