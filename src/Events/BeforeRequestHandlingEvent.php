<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class BeforeRequestHandlingEvent implements Contracts\WithApplication, Contracts\WithHttpRequest
{
    /**
     * @var ApplicationContract
     */
    private $app;

    /**
     * @var Request
     */
    private $request;

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

    /**
     * {@inheritDoc}
     */
    public function application(): ApplicationContract
    {
        return $this->app;
    }

    /**
     * {@inheritDoc}
     */
    public function httpRequest(): Request
    {
        return $this->request;
    }
}
