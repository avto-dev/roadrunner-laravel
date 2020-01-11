<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use Illuminate\Redis\RedisManager;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

class ResetRedisConnectionsListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $manager = $event->application()->make('redis');

            if ($manager instanceof RedisManager) {
                // @todo: write code
            }
        }
    }
}
