<?php

namespace AvtoDev\RoadRunnerLaravel\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetServerPortMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @see \Illuminate\Http\Request::url()
     * @see \Symfony\Component\HttpFoundation\Request::getPort()
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var int|string|null $port */
        $port = $request->getPort();

        if ($port === null || $port === '') {
            if ($request->getScheme() === 'https') {
                $request->server->set('SERVER_PORT', 443);
            } elseif ($request->getScheme() === 'http') {
                $request->server->set('SERVER_PORT', 80);
            }
        }

        return $next($request);
    }
}
