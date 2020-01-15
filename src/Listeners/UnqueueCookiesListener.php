<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/ResetCookie.php
 */
class UnqueueCookiesListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if (\is_object($event) && $event instanceof WithApplication) {
            $app = $event->application();

            if ($app->bound('cookie')) {
                /** @var \Illuminate\Cookie\CookieJar $cookies */
                $cookies = $app->make('cookie');

                foreach ($cookies->getQueuedCookies() as $key => $value) {
                    $cookies->unqueue($value->getName());
                }
            }
        }
    }
}
