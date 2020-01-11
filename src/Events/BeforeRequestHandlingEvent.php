<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class BeforeRequestHandlingEvent
{
    /**
     * @var ApplicationContract
     */
    public $app;

    /**
     * @var Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract $app
     * @param Request             $request
     */
    public function __construct(ApplicationContract $app, Request $request)
    {
        $this->app     = $app;
        $this->request = $request;
    }
}
