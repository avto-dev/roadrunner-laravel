<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Middleware\ForceHttpsMiddleware;

/**
 * @group  middleware
 *
 * @covers \AvtoDev\RoadRunnerLaravel\Middleware\ForceHttpsMiddleware<extended>
 */
class ForceHttpsMiddlewareTest extends AbstractTestCase
{
    /**
     * @var ForceHttpsMiddleware
     */
    protected $middleware;

    /**
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * @return void
     */
    public function testForceHttpsSchemaIfHeaderArePresents(): void
    {
        ($request = new Request)->headers->set('HTTPS', 'HTTPS');

        $this->assertSame('http://', $this->url_generator->formatScheme(null));

        $handled = false;

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        $this->assertSame('https://', $this->url_generator->formatScheme(null));
        $this->assertSame('on', $request->server->get('HTTPS'));
        $this->assertTrue($request->isSecure());

        $this->assertTrue($handled);
    }

    /**
     * @return void
     */
    public function testHttpsSchemaNotForcedIfHeaderAreNotPresents(): void
    {
        $request = new Request;

        $this->assertSame('http://', $this->url_generator->formatScheme(null));

        $handled = false;

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        // Nothing was changed
        $this->assertSame('http://', $this->url_generator->formatScheme(null));
        $this->assertFalse($request->server->has('HTTPS'));
        $this->assertFalse($request->isSecure());

        $this->assertTrue($handled);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware    = $this->app->make(ForceHttpsMiddleware::class);
        $this->url_generator = $this->app->make(UrlGenerator::class);
    }
}
