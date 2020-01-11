<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class BeforeLoopStartedEvent
{
    /**
     * @var ApplicationContract
     */
    public $app;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract $app
     */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }
}
