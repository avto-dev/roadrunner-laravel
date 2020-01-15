<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Events;

use Zend\Diactoros\ServerRequest;
use AvtoDev\RoadRunnerLaravel\Events\BeforeLoopIterationEvent;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithServerRequest;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Events\BeforeLoopIterationEvent<extended>
 */
class BeforeLoopIterationEventTest extends AbstractEventTestCase
{
    /**
     * @var string[]
     */
    protected $required_interfaces = [
        WithApplication::class,
        WithServerRequest::class,
    ];

    /**
     * @var string
     */
    protected $event_class = BeforeLoopIterationEvent::class;

    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new BeforeLoopIterationEvent(
            $this->app, $request = new ServerRequest
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($request, $event->serverRequest());
    }
}
