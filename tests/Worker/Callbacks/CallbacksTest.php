<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\Callbacks;

use Illuminate\Support\Traits\Macroable;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\Callbacks;
use AvtoDev\RoadRunnerLaravel\Support\Stacks\CallbacksStack;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;

class CallbacksTest extends AbstractTestCase
{
    /**
     * @var Callbacks
     */
    protected $callbacks;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->callbacks = new Callbacks;
    }

    /**
     * @return void
     */
    public function testInterfacesAndTraits(): void
    {
        $this->assertClassUsesTraits(Callbacks::class, Macroable::class);
        $this->assertInstanceOf(CallbacksInterface::class, $this->callbacks);
    }

    /**
     * @return void
     */
    public function testAccessorMethodsArePresented(): void
    {
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->afterHandleRequestStack());
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->afterLoopIterationStack());
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->beforeHandleRequestStack());
        $this->assertInstanceOf(CallbacksStack::class, $this->callbacks->beforeLoopIterationStack());
    }
}
