<?php

declare(strict_types=1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;
use function AvtoDev\RoadRunnerLaravel\isLumenEnvironment;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

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

            /** @var \Illuminate\Routing\Router|\Laravel\Lumen\Routing\Router $router */
            $router = $app->make('router');

            if (isLumenEnvironment()) {
                $router->app = $app; // @phpstan-ignore-line

                return;
            }

            // Black magic in action
            $reset_router = $this->getRebindRouteClosure($app, $request)->bindTo($router, $router);
            $reset_router();
        }
    }

    /**
     * @param Application|\Laravel\Lumen\Application $app
     * @param Request                                $request
     *
     * @return Closure
     */
    private static function getRebindRouteClosure($app, $request): Closure
    {
        return function () use ($app, $request) {
            $this->{'container'} = $app; // @phpstan-ignore-line

            try {
                /** @var mixed $route */
                $route = $this->{'getRoutes'}()->match($request); // @phpstan-ignore-line

                // rebind resolved controller
                if (\property_exists($route, $container_property = 'container')) {
                    $rebind_closure = function () use ($container_property, $app) {
                        $this->{$container_property} = $app; // @phpstan-ignore-line
                    };

                    $rebind = $rebind_closure->bindTo($route, $route);
                    $rebind();
                }

                // rebind matched route's container
                $route->setContainer($app);
            } catch (HttpException $e) {
                // do nothing
            }
        };
    }
}
