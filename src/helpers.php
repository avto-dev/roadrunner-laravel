<?php

namespace AvtoDev\RoadRunnerLaravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Check whether the current environment is of type Lumen.
 *
 * @return bool true if the environment equals to Lumen, false otherwise
 *
 * @internal
 */
function isLumenEnvironment(): bool
{
    return mb_strpos(app()->version(), 'Lumen') !== false;
}

/**
 * Returns the HTTP kernel.
 *
 * @param Application|\Laravel\Lumen\Application $app
 *
 * @throws BindingResolutionException
 *
 * @return HttpKernel|\Laravel\Lumen\Application
 *
 * @internal
 */
function getHttpKernel($app)
{
    if (isLumenEnvironment()) {
        return $app; // @phpstan-ignore-line
    }

    return $app->make(HttpKernel::class);
}
