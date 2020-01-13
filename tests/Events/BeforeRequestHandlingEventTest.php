<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Events;

use Illuminate\Http\Request;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;
use AvtoDev\RoadRunnerLaravel\Events\BeforeRequestHandlingEvent;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Events\BeforeRequestHandlingEvent<extended>
 */
class BeforeRequestHandlingEventTest extends AbstractEventTestCase
{
    /**
     * @var string[]
     */
    protected $required_interfaces = [
        WithApplication::class,
        WithHttpRequest::class,
    ];

    /**
     * @var string
     */
    protected $event_class = BeforeRequestHandlingEvent::class;

    /**
     * {@inheritDoc}
     */
    public function testConstructor(): void
    {
        $event = new BeforeRequestHandlingEvent(
            $this->app, $request = Request::create('/')
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($request, $event->httpRequest());
    }
}
