<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpResponse;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class AfterLoopIterationEvent implements WithApplication, WithHttpRequest, WithHttpResponse
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
     * @var Response
     */
    private $response;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract $app
     * @param Request             $request
     * @param Response            $response
     */
    public function __construct(ApplicationContract $app, Request $request, Response $response)
    {
        $this->app      = $app;
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function application(): ApplicationContract
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
