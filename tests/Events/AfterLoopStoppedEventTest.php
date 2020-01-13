<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Events;

use AvtoDev\RoadRunnerLaravel\Events\AfterLoopStoppedEvent;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Events\AfterLoopStoppedEvent<extended>
 */
class AfterLoopStoppedEventTest extends AbstractEventTestCase
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
    protected $event_class = AfterLoopStoppedEvent::class;

    /**
     * {@inheritDoc}
     */
    public function testConstructor(): void
    {
        $event = new AfterLoopStoppedEvent($this->app);

        $this->assertSame($this->app, $event->application());
    }
}
