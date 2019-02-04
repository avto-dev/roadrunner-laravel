<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Support\Stacks;

use Illuminate\Contracts\Support\Arrayable;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Support\Stacks\AbstractStack;
use AvtoDev\RoadRunnerLaravel\Support\Stacks\StackInterface;

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
        $this->assertClassUsesTraits($this->instance, \Illuminate\Support\Traits\Macroable::class);

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

        // First element getter
        $this->instance->clear();
        $this->instance->push($first = 'foo');
        $this->instance->push('bar');

        $this->assertSame($first, $this->instance->first());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $this->assertEquals($this->instance->all(), $this->instance->toArray());
    }

    /**
     * @return void
     */
    public function testFirstElementGetterThrownAnExceptionWhenStackIsEmpty()
    {
        $this->expectException(\LogicException::class);

        $this->instance->clear();
        $this->instance->first();
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
