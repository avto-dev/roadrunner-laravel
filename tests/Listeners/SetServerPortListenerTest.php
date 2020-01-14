<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\Listeners\SetServerPortListener;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\SetServerPortListener<extended>
 */
class SetServerPortListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $request = Request::create('http://127.0.0.1:123/foo');

        $this->assertSame($port = 123, $request->getPort());

        /** @var m\MockInterface|WithHttpRequest $event */
        $event = m::mock(WithHttpRequest::class)
            ->makePartial()
            ->expects('httpRequest')
            ->andReturn($request)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame($port, $request->server->get('SERVER_PORT'));
    }

    /**
     * {@inheritdoc}
     */
    public function testHandleNothingHappensWhenPortIsSetAsInteger(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', $port = 80);

        $this->assertSame($port, $request->getPort());

        /** @var m\MockInterface|WithHttpRequest $event */
        $event = m::mock(WithHttpRequest::class)
            ->makePartial()
            ->expects('httpRequest')
            ->andReturn($request)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame($port, $request->getPort());
        $this->assertSame($port, $request->server->get('SERVER_PORT'));
    }

    /**
     * {@inheritdoc}
     */
    public function testHandleNothingHappensWhenPortIsSetAsString(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', $port = '443');

        $this->assertSame($port, $request->getPort());

        /** @var m\MockInterface|WithHttpRequest $event */
        $event = m::mock(WithHttpRequest::class)
            ->makePartial()
            ->expects('httpRequest')
            ->andReturn($request)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame($port, $request->getPort());
        $this->assertSame($port, $request->server->get('SERVER_PORT'));
    }

    /**
     * {@inheritdoc}
     */
    public function testHandlePortSetAs443WhenSchemaIsHttpsAndServerPortIsNull(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', null);
        $request->server->set('HTTPS', 'on');

        $this->assertNull($request->getPort());

        /** @var m\MockInterface|WithHttpRequest $event */
        $event = m::mock(WithHttpRequest::class)
            ->makePartial()
            ->expects('httpRequest')
            ->andReturn($request)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame(443, $request->server->get('SERVER_PORT'));
        $this->assertSame(443, $request->getPort());
        $this->assertSame('https', $request->getScheme());
        $this->assertTrue($request->isSecure());
    }

    /**
     * {@inheritdoc}
     */
    public function testHandlePortSetAs80WhenSchemaIsHttpAndServerPortIsEmptyString(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', '');

        $this->assertSame('', $request->getPort());

        /** @var m\MockInterface|WithHttpRequest $event */
        $event = m::mock(WithHttpRequest::class)
            ->makePartial()
            ->expects('httpRequest')
            ->andReturn($request)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame(80, $request->server->get('SERVER_PORT'));
        $this->assertSame(80, $request->getPort());
        $this->assertSame('http', $request->getScheme());
        $this->assertFalse($request->isSecure());
    }

    /**
     * @return SetServerPortListener
     */
    protected function listenerFactory(): SetServerPortListener
    {
        return new SetServerPortListener;
    }
}
