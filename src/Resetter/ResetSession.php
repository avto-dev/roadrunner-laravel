<?php

namespace AvtoDev\RoadRunnerLaravel\Resetter;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Facade;
use ReflectionObject;

class ResetSession implements ResetterInterface
{
    /**
     * @inheritDoc
     */
    public function reset(Container $app): void
    {
        if ($app->has('session')) {
            $session = $app->make('session');
            $reference = new ReflectionObject($session);

            $drivers = $reference->getProperty('drivers');
            $drivers->setAccessible(true);
            $drivers->setValue($session, []);

            $app->forgetInstance('session.store');
            Facade::clearResolvedInstance('session.store');

            if ($app->has('redirect')) {
                /** @var Redirector $redirect */
                $redirect = $app->make('redirect');
                $redirect->setSession($app->make('session.store'));
            }
        }
    }
}
