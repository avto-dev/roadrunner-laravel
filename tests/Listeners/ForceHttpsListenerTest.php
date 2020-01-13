<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use AvtoDev\RoadRunnerLaravel\Listeners\ForceHttpsListener;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\ForceHttpsListener<extended>
 */
class ForceHttpsListenerTest extends AbstractListenerTestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * @var ConfigRepository
     */
    protected $config;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->request       = $this->app->make('request');
        $this->url_generator = $this->app->make(UrlGenerator::class);
        $this->config        = $this->app->make(ConfigRepository::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $this->config->set('roadrunner.force_https', true);

        /** @var m\MockInterface|WithApplication|WithHttpRequest $event */
        $event = m::mock(\implode(',', [WithApplication::class, WithHttpRequest::class]))
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock()
            ->expects('httpRequest')
            ->andReturn($this->request)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame('https://unit-test', $this->url_generator->current());
        $this->assertSame('on', $this->request->server->get('HTTPS'));
    }

    /**
     * {@inheritdoc}
     */
    public function testHandleWithoutForcing(): void
    {
        $this->config->set('roadrunner.force_https', false);

        /** @var m\MockInterface|WithApplication|WithHttpRequest $event */
        $event = m::mock(\implode(',', [WithApplication::class, WithHttpRequest::class]))
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event);

        $this->assertSame('http://unit-test', $this->url_generator->current());
        $this->assertNull($this->request->server->get('HTTPS'));
    }

    /**
     * @return ForceHttpsListener
     */
    protected function listenerFactory(): ForceHttpsListener
    {
        return new ForceHttpsListener;
    }
}
