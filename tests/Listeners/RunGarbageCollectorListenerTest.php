<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use AvtoDev\RoadRunnerLaravel\Listeners\RunGarbageCollectorListener;

/**
 * @coversNothing
 */
class RunGarbageCollectorListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $this->listenerFactory()->handle(new \stdClass);

        $this->markTestSkipped('Not tested yet');
    }

    /**
     * @return RunGarbageCollectorListener
     */
    protected function listenerFactory(): RunGarbageCollectorListener
    {
        return new RunGarbageCollectorListener;
    }
}
