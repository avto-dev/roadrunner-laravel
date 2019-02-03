<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\StartOptions;

use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptions;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;

/**
 * @coversDefaultClass \AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptions
 */
class StartOptionsTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testConstants()
    {
        $this->assertSame('not-', StartOptions::BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX);
        $this->assertSame('--', StartOptions::OPTIONS_PREFIX);
    }

    /**
     * @return void
     */
    public function testHasOption()
    {
        $cases = [
            $valid_options = ['--foo', '--123', '--321-not'],
            $valid_negative_options = ['--not-bar'],

            $invalid_options = ['baz', '666', 'foo123'],
            $invalid_negative_options = ['not-bzz', 'not123', 'not-678'],
        ];

        $start_options = new StartOptions(\array_merge(...$cases));

        foreach ($valid_options as $valid_option) {
            $this->assertTrue($start_options->hasOption(\mb_substr($valid_option, 2)));
        }

        foreach ($valid_negative_options as $valid_negative_option) {
            $this->assertTrue($start_options->hasOption(\mb_substr($valid_negative_option, 6)));
        }

        foreach ($invalid_options as $invalid_option) {
            $this->assertFalse($start_options->hasOption(\mb_substr($invalid_option, 2)));
            $this->assertFalse($start_options->hasOption($invalid_option));
        }

        foreach ($invalid_negative_options as $invalid_negative_option) {
            $this->assertFalse($start_options->hasOption(\mb_substr($invalid_negative_option, 6)));
            $this->assertFalse($start_options->hasOption(\mb_substr($invalid_negative_option, 4)));
            $this->assertFalse($start_options->hasOption($invalid_negative_option));
        }
    }
}
