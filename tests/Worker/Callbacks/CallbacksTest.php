<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\Callbacks;

use AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\Callbacks;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;
use Illuminate\Support\Traits\Macroable;

class CallbacksTest extends AbstractTestCase
{
    /**
     * @var Callbacks
     */
    protected $callbacks;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->callbacks = new Callbacks;
    }

    /**
     * @return void
     */
    public function testInterfacesAndTraits()
    {
        $this->assertClassUsesTraits(Callbacks::class, Macroable::class);
        $this->assertInstanceOf(CallbacksInterface::class, $this->callbacks);
    }

    /**
     * @return void
     */
    public function testAccessorMethodsArePresented()
    {
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->afterHandleRequestStack());
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->afterLoopStack());
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->beforeHandleRequestStack());
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->beforeLoopStack());
    }
}
