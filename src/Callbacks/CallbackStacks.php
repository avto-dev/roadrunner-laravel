<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Callbacks;

use AvtoDev\RoadRunnerLaravel\Stacks\CallbacksStack;

class CallbackStacks
{
    /**
     * @var CallbacksStack
     */
    protected $before_loop;

    /**
     * @var CallbacksStack
     */
    protected $before_handle_request;

    /**
     * @var CallbacksStack
     */
    protected $after_handle_request;

    /**
     * @var CallbacksStack
     */
    protected $after_loop;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->before_loop           = new CallbacksStack;
        $this->before_handle_request = new CallbacksStack;
        $this->after_handle_request  = new CallbacksStack;
        $this->after_loop            = new CallbacksStack;
    }

    /**
     * @return CallbacksStack
     */
    public function getAfterHandleRequestStack(): CallbacksStack
    {
        return $this->after_handle_request;
    }

    /**
     * @return CallbacksStack
     */
    public function getAfterLoopStack(): CallbacksStack
    {
        return $this->after_loop;
    }

    /**
     * @return CallbacksStack
     */
    public function getBeforeHandleRequestStack(): CallbacksStack
    {
        return $this->before_handle_request;
    }

    /**
     * @return CallbacksStack
     */
    public function getBeforeLoopStack(): CallbacksStack
    {
        return $this->before_loop;
    }
}
