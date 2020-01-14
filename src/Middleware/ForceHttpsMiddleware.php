<?php

namespace AvtoDev\RoadRunnerLaravel\Middleware;

use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializerInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class ForceHttpsMiddleware
{
    /**
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * Create a new middleware instance.
     *
     * @param UrlGenerator $url_generator
     */
    public function __construct(UrlGenerator $url_generator)
    {
        $this->url_generator = $url_generator;
    }

    /**
     * Handle an incoming request.
     *
     * @see \AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializer::initForceHttps()
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader(CallbacksInitializerInterface::FORCE_HTTPS_HEADER_NAME)) {
            $this->url_generator->forceScheme('https');

            // Set 'HTTPS' server parameter (required for correct working request methods like ::isSecure and others)
            $request->server->set('HTTPS', 'on');
        }

        return $next($request);
    }
}
