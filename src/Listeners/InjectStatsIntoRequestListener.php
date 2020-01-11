<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

class InjectStatsIntoRequestListener implements ListenerInterface
{
    /**
     * Incoming request macros name for accessing to the timestamp.
     */
    public const REQUEST_TIMESTAMP_MACRO = 'getTimestamp';

    /**
     * Incoming request macros name for accessing to the allocated memory size.
     */
    public const REQUEST_ALLOCATED_MEMORY_MACRO = 'getAllocatedMemory';

    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithHttpRequest) {
            $request = $event->httpRequest();

            if ($request instanceof \Illuminate\Http\Request) {
                $current_time     = \microtime(true);
                $allocated_memory = \memory_get_usage();

                $request::macro(self::REQUEST_TIMESTAMP_MACRO, function () use ($current_time): float {
                    return (float) $current_time;
                });

                $request::macro(self::REQUEST_ALLOCATED_MEMORY_MACRO, function () use ($allocated_memory): int {
                    return (int) $allocated_memory;
                });
            }
        }
    }
}
