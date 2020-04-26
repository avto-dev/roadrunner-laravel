<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpResponse;

final class AfterLoopIterationEvent implements WithApplication, WithHttpRequest, WithHttpResponse
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
     * @var Response
     */
    private $response;

    /**
     * Create a new event instance.
     *
     * @param Application|\Laravel\Lumen\Application $app
     * @param Request                                $request
     * @param Response                               $response
     */
    public function __construct($app, Request $request, Response $response)
    {
        $this->app      = $app;
        $this->request  = $request;
        $this->response = $response;
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

    /**
     * {@inheritdoc}
     */
    public function httpResponse(): Response
    {
        return $this->response;
    }
}
