<?php

namespace AvtoDev\RoadRunnerLaravel\Events\Contracts;

interface WithApplication
{
    /**
     * Get application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    public function application();
}
