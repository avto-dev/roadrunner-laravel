<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\Callbacks;

use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\Callbacks;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;
use Illuminate\Support\Traits\Macroable;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Worker\Callbacks\Callbacks<extended>
 */
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
        $after_handle  = $this->callbacks->afterHandleRequestStack();
        $after_loop    = $this->callbacks->afterLoopIterationStack();
        $before_handle = $this->callbacks->beforeHandleRequestStack();
        $before_loop   = $this->callbacks->beforeLoopIterationStack();

        $this->assertNotSame($after_handle, $after_loop);
        $this->assertNotSame($before_handle, $before_loop);
        $this->assertNotSame($after_handle, $before_handle);
    }
}
