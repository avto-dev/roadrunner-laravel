<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Support\Stacks;

use AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack
 *
 * @group stacks
 */
class CallbacksStackTest extends AbstractStackTestCase
{
    /**
     * @var CallbacksStack
     */
    protected $instance;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function testMakeMethod()
    {
        $callable = function () {
        };

        $this->assertEquals(
            $this->instanceFactory([$callable]), $this->instance::make([$callable])
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function testStackAccessors()
    {
        $this->assertCount(0, $this->instance);

        $this->instance->push($value = function (): bool {
            return true;
        });
        $this->instance->push($value2 = [$this, 'createApplication']);
        $this->instance->push('foo'); // must be ignored
        $this->instance->push(new \stdClass); // must be ignored

        $this->assertEquals([$value, $value2], $this->instance->all());
        $this->assertCount(2, $this->instance);

        // Test array iterator
        $_value = null;
        foreach ($this->instance as $item) {
            $_value = $item;
        }
        $this->assertEquals($value2, $_value);

        $this->instance->clear();

        $this->assertCount(0, $this->instance);
        $this->assertEquals([], $this->instance->all());
    }

    /**
     * @return void
     */
    public function testConstructorWithInvalidElements()
    {
        $instance = $this->instanceFactory([
            'foo',
            $callable_1 = function () {
            },
            $callable_2 = function (): bool {
                return false;
            },
            'bar',
        ]);

        $this->assertEquals([$callable_1, $callable_2], $instance->all());
    }

    /**
     * @return void
     */
    public function testCallEach()
    {
        $counter = 0;

        $this->instance->push($callable_1 = function ($val_1, $val_2) use (&$counter) {
            $this->assertEquals(1, $val_1);
            $this->assertEquals('foo', $val_2);

            $counter++;
        });

        $this->instance->push($callable_2 = function ($val_1, $val_2) use (&$counter) {
            $this->assertEquals(1, $val_1);
            $this->assertEquals('foo', $val_2);

            $counter++;
        });

        $this->instance->callEach(1, 'foo');
        $this->assertEquals(2, $counter);
    }

    /**
     * {@inheritdoc}
     *
     * @return CallbacksStack
     */
    protected function instanceFactory(...$arguments): CallbacksStack
    {
        return new CallbacksStack(...$arguments);
    }
}
