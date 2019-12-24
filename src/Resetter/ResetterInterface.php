<?php

namespace AvtoDev\RoadRunnerLaravel\Resetter;

use Illuminate\Container\Container;

interface ResetterInterface
{
    /**
     * Handles resetting of the application.
     *
     * @param Container $app
     */
    public function reset(Container $app): void;
}
