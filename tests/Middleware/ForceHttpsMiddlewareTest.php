<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Middleware;

use AvtoDev\RoadRunnerLaravel\Middleware\ForceHttpsMiddleware;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->middleware = $this->app->make(ForceHttpsMiddleware::class);
        $this->url_generator = $this->app->make(UrlGenerator::class);
    }

    /**
     * @return void
     */
    public function testForceHttpsSchemaIfHeaderArePresents()
    {
        ($request = new Request)->headers->set('HTTPS', 'HTTPS');

        $this->assertSame('http://', $this->url_generator->formatScheme());

        $handled = false;

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        $this->assertSame('https://', $this->url_generator->formatScheme());

        $this->assertTrue($handled);
    }

    /**
     * @return void
     */
    public function testHttpsSchemaNotForcedIfHeaderAreNotPresents()
    {
        $request = new Request;

        $this->assertSame('http://', $this->url_generator->formatScheme());

        $handled = false;

        $this->middleware->handle($request, function (Request $request) use (&$handled) {
            $handled = true;

            return $request;
        });

        // Nothing was changed
        $this->assertSame('http://', $this->url_generator->formatScheme());

        $this->assertTrue($handled);
    }
}
