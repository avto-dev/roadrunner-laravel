<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\CallbacksInitializer;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Traits\Macroable;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use Illuminate\Config\Repository as ConfigRepository;
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
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($_ENV['APP_FORCE_HTTPS']);

        parent::tearDown();
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
    public function testAutoInitMethodsCalling()
    {
        $mock                  = new class(new StartOptions(['--bla-bla']), $this->callbacks) extends CallbacksInitializer {
            public $called     = false;

            public $should_not = false;

            protected function initBlaBla()
            {
                $this->called = true;
            }

            protected function initFooBar()
            {
                $this->called = true;
            }
        };

        $mock->makeInit();

        $this->assertTrue($mock->called);
        $this->assertFalse($mock->should_not);
    }

    /**
     * @return void
     */
    public function testDefaultActionsInitialized()
    {
        // Get default closures without touching $this->.. instances
        $callbacks = new Callbacks;
        $this->callMethod(new CallbacksInitializer(new StartOptions, $callbacks), 'defaults', [$callbacks]);
        $first_closure  = $callbacks->afterLoopIterationStack()->first();
        $second_closure = $callbacks->beforeHandleRequestStack()->first();

        // And now - call same methods using $this->.. instances
        $this->initializer->makeInit();

        $this->assertSame(
            $this::getClosureHash($first_closure),
            $this::getClosureHash($this->callbacks->afterLoopIterationStack()->first())
        );
        $this->assertSame(
            $this::getClosureHash($second_closure),
            $this::getClosureHash($this->callbacks->beforeHandleRequestStack()->first())
        );

        // Test call
        $first_closure($this->app, new Request, new Response);

        ($request = new Request)->headers->set(CallbacksInitializer::FORCE_HTTPS_HEADER_NAME, 'true');
        $second_closure($this->app, $request);
        $this->assertFalse($request->headers->has(CallbacksInitializer::FORCE_HTTPS_HEADER_NAME));
    }

    /**
     * @return void
     */
    public function testDefaultActionsWithForceHttpsEnvValue()
    {
        $_ENV['APP_FORCE_HTTPS'] = true;

        $this->initializer->makeInit();

        $closure = $this->callbacks->beforeHandleRequestStack()->all()[1];

        $closure($this->app, $request = new Request);
        $this->assertSame('HTTPS', $request->headers->get('HTTPS'));
    }

    /**
     * @return void
     */
    public function testInitForceHttpsWithPassingTrue()
    {
        $this->callMethod($this->initializer, 'initForceHttps', [$this->callbacks, true]);
        $closure = $this->callbacks->beforeHandleRequestStack()->first();

        $closure($this->app, $request = new Request);
        $this->assertSame('HTTPS', $request->headers->get('HTTPS'));
    }

    /**
     * @return void
     */
    public function testInitForceHttpsWithPassingFalseButExistingSpecialExternalHeader()
    {
        $this->callMethod($this->initializer, 'initForceHttps', [$this->callbacks, false]);
        $closure = $this->callbacks->beforeHandleRequestStack()->first();

        ($request = new Request)->headers->set('FORCE-HTTPS', 'true');
        $closure($this->app, $request);
        $this->assertSame('HTTPS', $request->headers->get('HTTPS'));
    }

    /**
     * @return void
     */
    public function testInitForceHttpsWithPassingFalse()
    {
        $this->callMethod($this->initializer, 'initForceHttps', [$this->callbacks, false]);
        $closure = $this->callbacks->beforeHandleRequestStack()->first();

        $closure($this->app, $request = new Request);
        $this->assertFalse($request->headers->has('HTTPS'));
    }

    /**
     * @return void
     */
    public function testResetDbConnectionsWithPassingTrue()
    {
        /** @var ConfigRepository $config */
        $config = $this->app->make('config');
        /** @var DatabaseManager $db_manager */
        $db_manager = $this->app->make('db');

        $config->set('database.default', $connection_name = 'sqlite');
        $config->set("database.connections.{$connection_name}", [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $db_manager->connection($connection_name)->reconnect();

        $this->assertInstanceOf(\PDO::class, $db_manager->connection($connection_name)->getPdo());

        $this->callMethod($this->initializer, 'initResetDbConnections', [$this->callbacks, true]);
        $closure = $this->callbacks->afterLoopIterationStack()->first();
        $closure($this->app, new Request, new Response);

        $this->assertNull($db_manager->connection($connection_name)->getPdo());
    }

    /**
     * @return void
     */
    public function testResetDbConnectionsWithPassingFalse()
    {
        /** @var ConfigRepository $config */
        $config = $this->app->make('config');
        /** @var DatabaseManager $db_manager */
        $db_manager = $this->app->make('db');

        $config->set('database.default', $connection_name = 'sqlite');
        $config->set("database.connections.{$connection_name}", [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $db_manager->connection($connection_name)->reconnect();

        $this->assertInstanceOf(\PDO::class, $db_manager->connection($connection_name)->getPdo());

        $this->callMethod($this->initializer, 'initResetDbConnections', [$this->callbacks, false]);
        $this->assertEmpty($this->callbacks->afterLoopIterationStack());

        $this->assertInstanceOf(\PDO::class, $db_manager->connection($connection_name)->getPdo());
    }

    /**
     * @return void
     */
    public function testResetDebugInfoWithPassingTrue()
    {
        $this->callMethod($this->initializer, 'initResetDebugInfo', [$this->callbacks, true]);
        $closure = $this->callbacks->afterLoopIterationStack()->first();
        $closure($this->app); // Test direct calling
        $this->assertInstanceOf(\Closure::class, $closure);
    }

    /**
     * @return void
     */
    public function testResetDebugInfoWithPassingFalse()
    {
        $this->callMethod($this->initializer, 'initResetDebugInfo', [$this->callbacks, false]);
        $this->assertEmpty($this->callbacks->afterLoopIterationStack());
    }

    /**
     * @return void
     */
    public function testResetRedisConnectionsWithPassingTrue()
    {
        $this->callMethod($this->initializer, 'initResetRedisConnections', [$this->callbacks, true]);
        $closure = $this->callbacks->afterLoopIterationStack()->first();
        $closure($this->app, new Request, new Response); // Test direct calling
        $this->assertInstanceOf(\Closure::class, $closure);
    }

    /**
     * @return void
     */
    public function testResetRedisConnectionsWithPassingFalse()
    {
        $this->callMethod($this->initializer, 'initResetRedisConnections', [$this->callbacks, false]);
        $this->assertEmpty($this->callbacks->afterLoopIterationStack());
    }
}
