<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Foundation\Application;

final class BeforeRequestHandlingEvent implements Contracts\WithApplication, Contracts\WithHttpRequest
{
    /**
     * @var Application|\Laravel\Lumen\Application
     */
    private $app;

    /**
     * @var Request
     */
    private $request;

    /**
     * Create a new event instance.
     *
     * @param Application|\Laravel\Lumen\Application $app
     * @param Request                                $request
     */
    public function __construct($app, Request $request)
    {
        $this->app     = $app;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function application()
    {
        return $this->app;
    }

    /**
     * {@inheritdoc}
     */
    public function httpRequest(): Request
    {
        return $this->request;
    }
}
