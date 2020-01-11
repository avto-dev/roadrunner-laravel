<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/RebindRouterContainer.php
 */
class RebindRouterListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication && $event instanceof WithHttpRequest) {
            $app     = $event->application();
            $request = $event->httpRequest();

            /** @var \Illuminate\Routing\Router $router */
            $router  = $app->make('router');

            $closure = function () use ($app, $request) {
                $this->{'container'} = $app;

                try {
                    /** @var mixed $route */
                    $route = $this->{'routes'}->match($request);

                    // clear resolved controller
                    if (\property_exists($route, $container_property = 'container')) {
                        $route->{$container_property} = null;
                    }

                    // rebind matched route's container
                    $route->setContainer($app);
                } catch (NotFoundHttpException $e) {
                    // do nothing
                }
            };

            // Black magic in action
            $resetRouter = $closure->bindTo($router, $router);
            $resetRouter();
        }
    }
}
