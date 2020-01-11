<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class AfterRequestHandlingEvent implements Contracts\WithApplication,
                                                 Contracts\WithHttpRequest,
                                                 Contracts\WithHttpResponse
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

    /**
     * {@inheritDoc}
     */
    public function httpResponse(): Response
    {
        return $this->response;
    }
}
