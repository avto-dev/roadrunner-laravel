<?php

namespace AvtoDev\RoadRunnerLaravel\Worker\Callbacks;

use AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack;

interface CallbacksInterface
{
    /**
     * @return CallbacksStack
     */
    public function afterHandleRequestStack(): CallbacksStack;

    /**
     * @return CallbacksStack
     */
    public function afterLoopStack(): CallbacksStack;

    /**
     * @return CallbacksStack
     */
    public function beforeHandleRequestStack(): CallbacksStack;

    /**
     * @return CallbacksStack
     */
    public function beforeLoopStack(): CallbacksStack;
}
