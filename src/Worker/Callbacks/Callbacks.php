<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Worker\Callbacks;

use AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack;

class Callbacks implements CallbacksInterface
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
     * {@inheritdoc}
     */
    public function afterHandleRequestStack(): CallbacksStack
    {
        return $this->after_handle_request;
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoopStack(): CallbacksStack
    {
        return $this->after_loop;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeHandleRequestStack(): CallbacksStack
    {
        return $this->before_handle_request;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoopStack(): CallbacksStack
    {
        return $this->before_loop;
    }
}
