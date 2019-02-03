<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerWorkerLaravel\Settings;

class Settings implements SettingsInterface
{
    /**
     * Options this this prefix will invert own logic (true -> false).
     */
    const BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX = 'not-';

    /**
     * Options prefix.
     */
    const OPTIONS_PREFIX = '--';

    /**
     * Available options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Settings constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->options = $this->parseBooleanArgumentsList($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(string $option_name): bool
    {
        return \array_key_exists($option_name, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(string $option_name, $value): bool
    {
        if (\is_scalar($value) && $this->validateOptionName($option_name)) {
            $this->options[$option_name] = $value;

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Validate option name.
     *
     * @param string $option_name Option name
     *
     * @return bool
     */
    protected function validateOptionName(string $option_name): bool
    {
        return \preg_match('~^[a-zA-Z0-9\-_]+$~', $option_name) === 1;
    }

    /**
     * Parse passed into this method list of arguments, and returns an array with normalized boolean values.
     *
     * For example: ['--one', '--not-two', 'foo', 'blah bar'] will be converted to ['one' => true, 'two' => false].
     *
     * @param string[] $arguments Array of arguments
     *
     * @return bool[]
     */
    protected function parseBooleanArgumentsList(array $arguments): array
    {
        $options_prefix_length         = \mb_strlen(static::OPTIONS_PREFIX);
        $logic_inversion_prefix_length = \mb_strlen(static::BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX);
        $result                        = [];

        foreach ($arguments as $argument) {
            // Works only with prefixed arguments
            if (\is_string($argument) && \mb_strpos($argument, static::OPTIONS_PREFIX) === 0) {
                // Skip arguments with wrong characters
                if (! $this->validateOptionName($argument)) {
                    continue;
                }

                // Remove option prefix from argument
                $argument = \mb_substr($argument, $options_prefix_length);
                // By default - value is "true"
                $value = true;

                // Try to detect logic inversion prefix
                if (\mb_strpos($argument, static::BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX) === 0) {
                    $value = false;

                    // Remove logic inversion prefix from argument
                    $argument = \mb_substr($argument, $logic_inversion_prefix_length);
                }

                $result[$argument] = $value;
            }
        }

        return $result;
    }
}
