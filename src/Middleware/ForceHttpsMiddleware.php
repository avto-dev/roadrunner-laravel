<?php

namespace AvtoDev\RoadRunnerLaravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializerInterface;

class ForceHttpsMiddleware
{
    /**
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * Middleware constructor.
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
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader(CallbacksInitializerInterface::FORCE_HTTPS_HEADER_NAME)) {
            $this->url_generator->forceScheme('https');
        }

        return $next($request);
    }
}
