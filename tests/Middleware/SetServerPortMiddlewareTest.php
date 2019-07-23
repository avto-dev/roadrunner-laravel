<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Middleware;

use Illuminate\Http\Request;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Middleware\SetServerPortMiddleware;

/**
 * @group  middleware
 *
 * @covers \AvtoDev\RoadRunnerLaravel\Middleware\SetServerPortMiddleware
 */
class SetServerPortMiddlewareTest extends AbstractTestCase
{
    /**
     * @var SetServerPortMiddleware
     */
    protected $middleware;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = $this->app->make(SetServerPortMiddleware::class);
    }

    /**
     * @return void
     */
    public function testNothingHappensWhenPostIsSetAsInteger(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', $port = 80);

        $handled = false;

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        $this->assertSame($port, $request->server->get('SERVER_PORT'));
        $this->assertSame($port, $request->getPort());

        $this->assertTrue($handled);
    }

    /**
     * @return void
     */
    public function testNothingHappensWhenPostIsSetAsString(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', $port = '443');

        $handled = false;

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        $this->assertSame($port, $request->server->get('SERVER_PORT'));
        $this->assertSame($port, $request->getPort());

        $this->assertTrue($handled);
    }

    /**
     * @return void
     */
    public function testPortSetAs443WhenSchemaIsHttpsAndServerPortIsNull(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', null);
        $request->server->set('HTTPS', 'on');

        $handled = false;

        $this->assertNull($request->getPort());

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        $this->assertSame(443, $request->server->get('SERVER_PORT'));
        $this->assertSame(443, $request->getPort());
        $this->assertSame('https', $request->getScheme());
        $this->assertTrue($request->isSecure());

        $this->assertTrue($handled);
    }

    /**
     * @return void
     */
    public function testPortSetAs80WhenSchemaIsHttpAndServerPortIsEmptyString(): void
    {
        ($request = new Request)->server->set('SERVER_PORT', '');

        $handled = false;

        $this->assertSame('', $request->getPort());

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        $this->assertSame(80, $request->server->get('SERVER_PORT'));
        $this->assertSame(80, $request->getPort());
        $this->assertSame('http', $request->getScheme());
        $this->assertFalse($request->isSecure());

        $this->assertTrue($handled);
    }
}
