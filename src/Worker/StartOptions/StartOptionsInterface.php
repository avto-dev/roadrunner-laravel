<?php

namespace AvtoDev\RoadRunnerLaravel\Worker\StartOptions;

interface StartOptionsInterface
{
    /**
     * Returns true if an options array contains option by name.
     *
     * @param string $option_name Option name
     *
     * @return bool
     */
    public function hasOption(string $option_name): bool;

    /**
     * Set an option.
     *
     * @param string $option_name Option name
     * @param mixed  $value       Scalar type only
     *
     * @return bool
     */
    public function setOption(string $option_name, $value): bool;

    /**
     * Get option by name.
     *
     * @param string $option_name Option name
     *
     * @return mixed
     */
    public function getOption(string $option_name);

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions(): array;
}
