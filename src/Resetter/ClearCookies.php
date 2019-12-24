<?php

namespace AvtoDev\RoadRunnerLaravel\Resetter;

use Illuminate\Container\Container;
use Illuminate\Cookie\CookieJar;

class ClearCookies implements ResetterInterface
{
    /**
     * @inheritDoc
     */
    public function reset(Container $app): void
    {
        if ($app->has('cookie')) {
            /** @var CookieJar $cookies */
            $cookies = $app->make('cookie');

            foreach ($cookies->getQueuedCookies() as $value) {
                $cookies->unqueue($value->getName());
            }
        }
    }
}
