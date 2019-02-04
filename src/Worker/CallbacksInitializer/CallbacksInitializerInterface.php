<?php

namespace AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer;

use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;
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
     * @param CallbacksInterface    $callback_stacks
     */
    public function __construct(StartOptionsInterface $start_options, CallbacksInterface $callback_stacks);

    /**
     * Make initialization.
     *
     * IMPORTANT! You should call this method manually after instance constructor calling.
     *
     * @return void
     */
    public function makeInit();
}
