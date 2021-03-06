<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Events;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpResponse;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent<extended>
 */
class AfterLoopIterationEventTest extends AbstractEventTestCase
{
    /**
     * @var string[]
     */
    protected $required_interfaces = [
        WithApplication::class,
        WithHttpRequest::class,
        WithHttpResponse::class,
    ];

    /**
     * @var string
     */
    protected $event_class = AfterLoopIterationEvent::class;

    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new AfterLoopIterationEvent(
            $this->app, $request = Request::create('/'), $response = Response::create()
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($request, $event->httpRequest());
        $this->assertSame($response, $event->httpResponse());
    }
}
