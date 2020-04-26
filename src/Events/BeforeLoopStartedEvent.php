<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Illuminate\Contracts\Foundation\Application;

final class BeforeLoopStartedEvent implements Contracts\WithApplication
{
    /**
     * @var Application|\Laravel\Lumen\Application
     */
    private $app;

    /**
     * Create a new event instance.
     *
     * @param Application|\Laravel\Lumen\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function application()
    {
        return $this->app;
    }
}
