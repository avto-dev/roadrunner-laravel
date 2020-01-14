<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use AvtoDev\RoadRunnerLaravel\Listeners\ResetRedisConnectionsListener;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\ResetRedisConnectionsListener<extended>
 */
class ResetRedisConnectionsListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $this->markTestSkipped('Listener not ready yet');
    }

    /**
     * @return ResetRedisConnectionsListener
     */
    protected function listenerFactory(): ResetRedisConnectionsListener
    {
        return new ResetRedisConnectionsListener;
    }
}
