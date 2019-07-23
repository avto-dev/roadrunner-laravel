<?php

namespace AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer;

use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptionsInterface;

interface CallbacksInitializerInterface
{
    /**
     * This header using for telling application "force use https schema!".
     */
    public const FORCE_HTTPS_HEADER_NAME = 'HTTPS';

    /**
     * Header for forcing https schema by incoming (**external**) request header.
     */
    public const FORCE_HTTPS_EXTERNAL_HEADER_NAME = 'FORCE-HTTPS';

    /**
     * Incoming request object macros name for accessing to the "before request processed" timestamp.
     */
    public const REQUEST_TIMESTAMP_MACRO = 'getTimestamp';

    /**
     * Incoming request object macros name for accessing to the "before request processed" allocated memory size.
     */
    public const REQUEST_ALLOCATED_MEMORY_MACRO = 'getAllocatedMemory';

    /**
     * Option name for fixing symfony file validation.
     */
    public const FIX_SYMFONY_FILE_VALIDATION_OPTION = 'fix-symfony-file-validation';

    /**
     * Constructor.
     *
     * @param StartOptionsInterface $start_options
     * @param CallbacksInterface    $callbacks
     */
    public function __construct(StartOptionsInterface $start_options, CallbacksInterface $callbacks);

    /**
     * Make initialization.
     *
     * IMPORTANT! You should call this method manually after instance constructor calling.
     *
     * @return void
     */
    public function makeInit(): void;
}
