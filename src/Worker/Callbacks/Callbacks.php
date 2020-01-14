<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Worker\Callbacks;

use AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack;
use Illuminate\Support\Traits\Macroable;

class Callbacks implements CallbacksInterface
{
    use Macroable;

    /**
     * @var CallbacksStack
     */
    protected $before_loop_starts;

    /**
     * @var CallbacksStack
     */
    protected $before_loop_iteration;

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
    protected $after_loop_iteration;

    /**
     * @var CallbacksStack
     */
    protected $after_loop_ends;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->before_loop_starts    = new CallbacksStack;
        $this->before_loop_iteration = new CallbacksStack;
        $this->before_handle_request = new CallbacksStack;
        $this->after_handle_request  = new CallbacksStack;
        $this->after_loop_iteration  = new CallbacksStack;
        $this->after_loop_ends       = new CallbacksStack;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoopStarts(): CallbacksStack
    {
        return $this->before_loop_starts;
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
    public function afterLoopIterationStack(): CallbacksStack
    {
        return $this->after_loop_iteration;
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
    public function beforeLoopIterationStack(): CallbacksStack
    {
        return $this->before_loop_iteration;
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoopEnds(): CallbacksStack
    {
        return $this->after_loop_ends;
    }
}
