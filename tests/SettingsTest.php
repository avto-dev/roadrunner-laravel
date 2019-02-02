<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerWorkerLaravel\Tests;

use AvtoDev\RoadRunnerWorkerLaravel\Settings;

/**
 * @coversDefaultClass \AvtoDev\RoadRunnerWorkerLaravel\Settings
 */
class SettingsTest extends AbstractTestCase
{
    /**
     * @coversNothing
     *
     * @return void
     */
    public function testConstants()
    {
        $this->assertSame('not-', Settings::BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX);
        $this->assertSame('--', Settings::OPTIONS_PREFIX);
    }

    /**
     * @covers ::hasOption
     *
     * @return void
     */
    public function testHasOption()
    {
        $settings = new Settings([
            '--foo', '--not-bar', 'baz', '--123', '--321-not',
        ]);

        $this->assertTrue($settings->hasOption('foo'));
        $this->assertTrue($settings->hasOption('bar'));
        $this->assertTrue($settings->hasOption('123'));
        $this->assertTrue($settings->hasOption('321-not'));
        $this->assertFalse($settings->hasOption('baz'));
    }
}
