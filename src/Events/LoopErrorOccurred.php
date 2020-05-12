<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Events;

use Throwable;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithException;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

final class LoopErrorOccurred implements WithApplication, WithException
{
    /**
     * @var ApplicationContract
     */
    private $app;

    /**
     * @var Throwable
     */
    private $exception;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract $app
     * @param Throwable           $exception
     */
    public function __construct(ApplicationContract $app, Throwable $exception)
    {
        $this->app       = $app;
        $this->exception = $exception;
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
    public function exception(): Throwable
    {
        return $this->exception;
    }
}
