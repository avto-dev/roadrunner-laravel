<?php

namespace AvtoDev\RoadRunnerLaravel\Resetters;

interface ResetterInterface
{
    /**
     * @param string|object $event
     *
     * @return void
     */
    public function handle($event): void;

    /**
     * @return string[]
     */
    public static function listenForEvents(): array;
}
