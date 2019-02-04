<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\CallbacksInitializer;

use Illuminate\Support\Traits\Macroable;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\Callbacks;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptions;
use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializer;
use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializerInterface;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializer
 */
class CallbacksInitializerTest extends AbstractTestCase
{
    /**
     * @var CallbacksInitializer
     */
    protected $initializer;

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

        $this->callbacks   = new Callbacks;
        $this->initializer = new CallbacksInitializer(new StartOptions, $this->callbacks);
    }

    /**
     * @return void
     */
    public function testConstants()
    {
        $this->assertSame('init', CallbacksInitializer::RULE_METHOD_PREFIX);
        $this->assertSame('HTTPS', CallbacksInitializer::FORCE_HTTPS_HEADER_NAME);
    }

    /**
     * @return void
     */
    public function testInterfacesAndTraits()
    {
        $this->assertClassUsesTraits(CallbacksInitializer::class, Macroable::class);
        $this->assertInstanceOf(CallbacksInitializerInterface::class, $this->initializer);
    }

    /**
     * @return void
     */
    public function testDefaultActionsInitialized()
    {
        $callbacks = new Callbacks;

        $this->callMethod(new CallbacksInitializer(new StartOptions, $callbacks), 'defaults', [$callbacks]);

        $first_closure_hash   = $this::getClosureHash($callbacks->afterLoopStack()->first());
        $second_closure_hash  = $this::getClosureHash($callbacks->beforeHandleRequestStack()->first());

        $this->initializer->makeInit();

        $this->assertSame($first_closure_hash, $this::getClosureHash($this->callbacks->afterLoopStack()->first()));
        $this->assertSame($second_closure_hash, $this::getClosureHash($this->callbacks->beforeHandleRequestStack()->first()));
    }
}
