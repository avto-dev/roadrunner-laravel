<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/ResetSession.php
 */
class ResetSessionListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if (\is_object($event) && $event instanceof WithApplication) {
            $app = $event->application();

            if ($app->bound('session')) {
                /** @var \Illuminate\Session\SessionManager $session */
                $session = $app->make('session');
                $driver  = $session->driver();

                if ($driver instanceof \Illuminate\Contracts\Session\Session) {
                    $driver->flush();
                }

                if ($driver instanceof \Illuminate\Session\Store) {
                    $driver->regenerate();
                }
            }
        }
    }
}
