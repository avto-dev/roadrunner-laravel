<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\StartOptions;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptions;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptionsInterface;

/**
 * @group start_options
 */
class StartOptionsTest extends AbstractTestCase
{
    /**
     * @var StartOptions
     */
    protected $start_options;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->start_options = new StartOptions;
    }

    /**
     * @return void
     */
    public function testInterfacesAndTraits()
    {
        $this->assertClassUsesTraits(StartOptions::class, Macroable::class);
        $this->assertInstanceOf(StartOptionsInterface::class, $this->start_options);
    }

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

            $invalid_options = ['baz', '666', 'foo123', '~--aaa', '---bas evil'],
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

    /**
     * @return void
     */
    public function testSetAndGetOptionsPositiveMechanic()
    {
        $options_list = [
            'foo-bar' => true,
            'foo'     => false,
            'bar'     => 123,
            'bar-baz' => 3.14,
            'baz'     => 'ok dude',
        ];

        foreach ($options_list as $option_name => $option_value) {
            $this->assertFalse($this->start_options->hasOption($option_name));
            $this->start_options->setOption($option_name, $option_value);
            $this->assertTrue($this->start_options->hasOption($option_name));
            $this->assertSame($option_value, $this->start_options->getOption($option_name));
        }

        $this->assertSame($options_list, $this->start_options->getOptions());
    }

    /**
     * @return void
     */
    public function testSetAndGetOptionsNegativeMechanic()
    {
        $options_list = [
            'foo-bar' => [123],
            'foo'     => [true, 'foo'],
            'bar'     => \tmpfile(),
            'bar-baz' => new \stdClass,
        ];

        foreach ($options_list as $option_name => $option_value) {
            $this->assertFalse($this->start_options->hasOption($option_name));
            $this->start_options->setOption($option_name, $option_value);
            $this->assertFalse($this->start_options->hasOption($option_name));
        }

        $this->assertEmpty($this->start_options->getOptions());
    }

    /**
     * @return void
     */
    public function testExceptionShouldBeThrownWhileGettingUnknownOption()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('~Option.*not\sexists~is');

        $this->assertFalse($this->start_options->hasOption($not_exists_option_name = Str::random()));

        $this->start_options->getOption($not_exists_option_name);
    }
}
