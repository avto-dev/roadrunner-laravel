<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Events;

use AvtoDev\RoadRunnerLaravel\Events\BeforeLoopStartedEvent;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Events\BeforeLoopStartedEvent<extended>
 */
class BeforeLoopStartedEventTest extends AbstractEventTestCase
{
    /**
     * @var string[]
     */
    protected $required_interfaces = [
        WithApplication::class,
    ];

    /**
     * @var string
     */
    protected $event_class = BeforeLoopStartedEvent::class;

    /**
     * {@inheritDoc}
     */
    public function testConstructor(): void
    {
        $event = new BeforeLoopStartedEvent($this->app);

        $this->assertSame($this->app, $event->application());
    }
}
