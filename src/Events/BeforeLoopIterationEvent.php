<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Illuminate\Contracts\Foundation\Application;
use Psr\Http\Message\ServerRequestInterface;

final class BeforeLoopIterationEvent implements Contracts\WithApplication, Contracts\WithServerRequest
{
    /**
     * @var Application|\Laravel\Lumen\Application
     */
    private $app;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Create a new event instance.
     *
     * @param Application|\Laravel\Lumen\Application    $app
     * @param ServerRequestInterface $request
     */
    public function __construct($app, ServerRequestInterface $request)
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
    public function serverRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
