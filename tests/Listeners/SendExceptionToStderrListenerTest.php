<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use AvtoDev\RoadRunnerLaravel\Listeners\SendExceptionToStderrListener;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Listeners\SendExceptionToStderrListener<extended>
 */
class SendExceptionToStderrListenerTest extends AbstractListenerTestCase
{
    public function testHandle(): void
    {
        $this->listenerFactory()->handle(new \stdClass);

        $this->markTestIncomplete('There is no legal way for handle method testing.');
    }

    protected function listenerFactory()
    {
        return new SendExceptionToStderrListener;
    }
}
