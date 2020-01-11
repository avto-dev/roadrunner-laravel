<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class BeforeLoopIterationEvent
{
    /**
     * @var ApplicationContract
     */
    public $app;

    /**
     * @var ServerRequestInterface
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract    $app
     * @param ServerRequestInterface $request
     */
    public function __construct(ApplicationContract $app, ServerRequestInterface $request)
    {
        $this->app     = $app;
        $this->request = $request;
    }
}
