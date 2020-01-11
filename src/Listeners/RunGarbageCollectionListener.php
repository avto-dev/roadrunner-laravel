<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

class RunGarbageCollectionListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        \gc_collect_cycles(); // keep the memory low (this will slow down your application a bit)
    }
}