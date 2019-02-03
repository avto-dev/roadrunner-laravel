<?php

namespace AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer;

use AvtoDev\RoadRunnerLaravel\Worker\CallbackStacks;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptionsInterface;

interface CallbacksInitializerInterface
{
    /**
     * This header using for telling application "force use https schema!".
     */
    const FORCE_HTTPS_HEADER_NAME = 'HTTPS';

    /**
     * Constructor.
     *
     * @param StartOptionsInterface $start_options
     * @param CallbackStacks        $callback_stacks
     */
    public function __construct(StartOptionsInterface $start_options, CallbackStacks $callback_stacks);
}
