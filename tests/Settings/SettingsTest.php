<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerWorkerLaravel\Tests\Settings;

use AvtoDev\RoadRunnerWorkerLaravel\Settings\Settings;
use AvtoDev\RoadRunnerWorkerLaravel\Tests\AbstractTestCase;

/**
 * @coversDefaultClass \AvtoDev\RoadRunnerWorkerLaravel\Settings\Settings
 */
class SettingsTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testConstants()
    {
        $this->assertSame('not-', Settings::BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX);
        $this->assertSame('--', Settings::OPTIONS_PREFIX);
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

        $settings = new Settings(\array_merge(...$cases));

        foreach ($valid_options as $valid_option) {
            $this->assertTrue($settings->hasOption(\mb_substr($valid_option, 2)));
        }

        foreach ($valid_negative_options as $valid_negative_option) {
            $this->assertTrue($settings->hasOption(\mb_substr($valid_negative_option, 6)));
        }

        foreach ($invalid_options as $invalid_option) {
            $this->assertFalse($settings->hasOption(\mb_substr($invalid_option, 2)));
            $this->assertFalse($settings->hasOption($invalid_option));
        }

        foreach ($invalid_negative_options as $invalid_negative_option) {
            $this->assertFalse($settings->hasOption(\mb_substr($invalid_negative_option, 6)));
            $this->assertFalse($settings->hasOption(\mb_substr($invalid_negative_option, 4)));
            $this->assertFalse($settings->hasOption($invalid_negative_option));
        }
    }
}
