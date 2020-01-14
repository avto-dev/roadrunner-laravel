<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use AvtoDev\RoadRunnerLaravel\Listeners\RunGarbageCollectorListener;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\RunGarbageCollectorListener<extended>
 */
class RunGarbageCollectorListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $this->listenerFactory()->handle(new \stdClass);

        $this->assertTrue(true, 'There is no legal way for handle method testing.');
    }

    /**
     * @return RunGarbageCollectorListener
     */
    protected function listenerFactory(): RunGarbageCollectorListener
    {
        return new RunGarbageCollectorListener;
    }
}
