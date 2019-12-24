<?php

namespace AvtoDev\RoadRunnerLaravel\Resetter;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use ReflectionObject;

class ResetAuth implements ResetterInterface
{
    /**
     * @inheritDoc
     */
    public function reset(Container $app): void
    {
        if ($app->has('auth')) {
            $auth = $app->make('auth');
            $reference = new ReflectionObject($auth);

            if ($reference->hasProperty('guards')) {
                $guards = $reference->getProperty('guards');
            } else {
                $guards = $reference->getProperty('drivers');
            }

            $guards->setAccessible(true);
            $guards->setValue($auth, []);

            $app->forgetInstance('auth.driver');
            Facade::clearResolvedInstance('auth.driver');
        }
    }
}
