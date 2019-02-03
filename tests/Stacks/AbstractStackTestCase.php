<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Stacks;

use Illuminate\Contracts\Support\Arrayable;
use AvtoDev\RoadRunnerLaravel\Stacks\AbstractStack;
use AvtoDev\RoadRunnerLaravel\Stacks\StackInterface;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Stacks\AbstractStack
 *
 * @group  stacks
 */
abstract class AbstractStackTestCase extends AbstractTestCase
{
    /**
     * @var AbstractStack
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->instance = $this->instanceFactory();
    }

    /**
     * Test interfaces amd traits.
     *
     * @return void
     */
    public function testInterfacesAndTraits()
    {
        // Has \Illuminate\Support\Traits\Macroable trait methods
        foreach (['macro', 'mixin', 'hasMacro'] as $macro_method_name) {
            $this->assertTrue(\method_exists($this->instance, $macro_method_name));
        }

        $this->assertInstanceOf(StackInterface::class, $this->instance);

        // Deeper, deeper!
        $this->assertInstanceOf(Arrayable::class, $this->instance);
        $this->assertInstanceOf(\Countable::class, $this->instance);
        $this->assertInstanceOf(\IteratorAggregate::class, $this->instance);
    }

    /**
     * @return void
     */
    public function testMakeMethod()
    {
        $this->assertEquals(
            $this->instanceFactory(), $this->instance::make() // Test without arguments passing
        );
    }

    /**
     * Test stack accessors methods.
     *
     * @return void
     */
    public function testStackAccessors()
    {
        $this->assertCount(0, $this->instance);

        $this->instance->push($value = 'foo bar');

        $this->assertEquals([$value], $this->instance->all());
        $this->assertCount(1, $this->instance);

        // Test array iterator
        $_value = null;
        foreach ($this->instance as $item) {
            $_value = $item;
        }
        $this->assertEquals($value, $_value);

        $this->instance->clear();

        $this->assertCount(0, $this->instance);
        $this->assertEquals([], $this->instance->all());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $this->assertEquals($this->instance->all(), $this->instance->toArray());
    }

    /**
     * Fabric method for tested instances.
     *
     * @return StackInterface
     */
    protected function instanceFactory()
    {
        return new class extends AbstractStack {
            /**
             * {@inheritdoc}
             */
            public function push($item)
            {
                $this->items[] = $item;
            }
        };
    }
}
