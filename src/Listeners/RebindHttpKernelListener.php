<?php

declare(strict_types=1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use function AvtoDev\RoadRunnerLaravel\getHttpKernel;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/RebindKernelContainer.php
 */
class RebindHttpKernelListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            /** @var HttpKernel $kernel */
            $kernel = getHttpKernel($app);

            $closure = function () use ($app) {
                $this->{'app'} = $app;
            };

            // Black magic in action
            $reseter = $closure->bindTo($kernel, $kernel);
            $reseter();
        }
    }
}
