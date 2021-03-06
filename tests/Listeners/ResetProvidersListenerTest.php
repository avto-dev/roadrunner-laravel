<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use Mockery as m;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Listeners\ResetProvidersListener;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\ResetProvidersListener<extended>
 */
class ResetProvidersListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        FakeServiceProvider::$onConstructor = null;
        FakeServiceProvider::$onRegister    = null;
        FakeServiceProvider::$onBoot        = null;
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @var ConfigRepository $config */
        $config             = $this->app->make(ConfigRepository::class);
        $constructor_called = $register_called = $boot_called = false;

        $config->set('roadrunner.reset_providers', [$fake_provider = FakeServiceProvider::class]);

        FakeServiceProvider::$onConstructor = function (...$args) use (&$constructor_called) {
            $this->assertInstanceOf(Application::class, $args[0]);
            $constructor_called = true;
        };

        FakeServiceProvider::$onRegister = static function () use (&$register_called) {
            $register_called = true;
        };

        FakeServiceProvider::$onBoot = static function () use (&$boot_called) {
            $boot_called = true;
        };

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertTrue($constructor_called);
        $this->assertTrue($register_called);
        $this->assertTrue($boot_called);
    }

    /**
     * @return ResetProvidersListener
     */
    protected function listenerFactory(): ResetProvidersListener
    {
        return new ResetProvidersListener;
    }
}
