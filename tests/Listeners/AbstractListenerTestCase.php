<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Listeners\ListenerInterface;

abstract class AbstractListenerTestCase extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testImplementation(): void
    {
        $this->assertInstanceOf(ListenerInterface::class, $this->listenerFactory());
    }

    /**
     * Listener factory.
     *
     * @return ListenerInterface|mixed
     */
    abstract protected function listenerFactory();

    /**
     * Test listener `handle` method.
     *
     * @return void
     */
    abstract protected function testHandle(): void;
}
