<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Events;

use Zend\Diactoros\ServerRequest;
use AvtoDev\RoadRunnerLaravel\Events\LoopErrorOccurred;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithException;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithServerRequest;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Events\LoopErrorOccurred<extended>
 */
class LoopErrorOccurredTest extends AbstractEventTestCase
{
    /**
     * @var string[]
     */
    protected $required_interfaces = [
        WithApplication::class,
        WithException::class,
        WithServerRequest::class,
    ];

    /**
     * @var string
     */
    protected $event_class = LoopErrorOccurred::class;

    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new LoopErrorOccurred(
            $this->app,
            $request = new ServerRequest,
            $exception = new \Exception("foo")
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($exception, $event->exception());
        $this->assertSame($request, $event->serverRequest());
    }
}
