<?php

namespace AvtoDev\RoadRunnerLaravel\Resetter;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Facade;

class ResetRequest implements ResetterInterface
{
    /**
     * @inheritDoc
     */
    public function reset(Container $app): void
    {
        $app->forgetInstance('request');
        Facade::clearResolvedInstance('request');
    }
}
