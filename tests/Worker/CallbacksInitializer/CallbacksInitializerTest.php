<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker\CallbacksInitializer;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Traits\Macroable;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use Illuminate\Config\Repository as ConfigRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    protected function setUp(): void
    {
        parent::setUp();

        $this->callbacks   = new Callbacks;
        $this->initializer = new CallbacksInitializer(new StartOptions, $this->callbacks);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
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

        $this->assertSame('getTimestamp', CallbacksInitializer::REQUEST_TIMESTAMP_MACRO);
        $this->assertSame('getAllocatedMemory', CallbacksInitializer::REQUEST_ALLOCATED_MEMORY_MACRO);
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
    public function testDefaultActionCollectCycles()
    {
        $callbacks = new Callbacks;
        $this->callMethod(new CallbacksInitializer(new StartOptions, $callbacks), 'defaults', [$callbacks]);
        $closure = $callbacks->afterLoopIterationStack()->first();

        $this->initializer->makeInit();

        $this->assertSame(
            $this::getClosureHash($closure),
            $this::getClosureHash($this->callbacks->afterLoopIterationStack()->first())
        );

        // Test call
        $closure($this->app, new Request, new Response);
    }

    /**
     * @return void
     */
    public function testDefaultActionRemoveForceHttpsHeader()
    {
        $callbacks = new Callbacks;
        $this->callMethod(new CallbacksInitializer(new StartOptions, $callbacks), 'defaults', [$callbacks]);
        $remove_force_https_closure = $callbacks->beforeHandleRequestStack()->first();

        $this->initializer->makeInit();

        $this->assertSame(
            $this::getClosureHash($remove_force_https_closure),
            $this::getClosureHash($this->callbacks->beforeHandleRequestStack()->first())
        );

        ($request = new Request)->headers->set(CallbacksInitializer::FORCE_HTTPS_HEADER_NAME, 'true');
        $remove_force_https_closure($this->app, $request);
        $this->assertFalse($request->headers->has(CallbacksInitializer::FORCE_HTTPS_HEADER_NAME));
    }

    /**
     * @return void
     */
    public function testDefaultActionFixSymfonyFileValidation()
    {
        $callbacks = new Callbacks;
        $this->callMethod(new CallbacksInitializer(new StartOptions, $callbacks), 'defaults', [$callbacks]);
        $closure = $callbacks->beforeLoopStarts()->first();

        $this->initializer->makeInit();

        $this->assertSame(
            $this::getClosureHash($closure),
            $this::getClosureHash($this->callbacks->beforeLoopStarts()->first())
        );

        $tmp_file = \tempnam(\sys_get_temp_dir(), $file_name = Str::random(32));
        // If uncomment next line - "magic" doesn't work! (looks like PHP optimization in action)
        // $this->assertFalse((new UploadedFile($tmp_file, $file_name))->isValid());
        $this->assertFalse(\function_exists($func = '\\Symfony\\Component\\HttpFoundation\\File\\is_uploaded_file'));
        $closure($this->app);
        $this->assertTrue(\function_exists($func));
        $this->assertTrue((new UploadedFile($tmp_file, $file_name))->isValid());

        if (\is_file($tmp_file)) {
            \unlink($tmp_file);
        }
    }

    /**
     * @return void
     */
    public function testDefaultActionSkipSymfonyFileValidationFixing()
    {
        $callbacks = new Callbacks;
        $this->callMethod(new CallbacksInitializer(new StartOptions([
            '--not-fix-symfony-file-validation',
        ]), $callbacks), 'defaults', [$callbacks]);

        $this->initializer->makeInit();

        $this->assertCount(0, $callbacks->beforeLoopStarts());
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
    public function testUpdateAppStatsWithPassingTrue()
    {
        /** @var Request $request */
        $request = $this->app->make('request');

        $this->assertFalse($request::hasMacro($this->initializer::REQUEST_TIMESTAMP_MACRO));
        $this->assertFalse($request::hasMacro($this->initializer::REQUEST_ALLOCATED_MEMORY_MACRO));

        $this->callMethod($this->initializer, 'initInjectStatsIntoRequest', [$this->callbacks, true]);
        $closure = $this->callbacks->beforeHandleRequestStack()->first();
        $closure($this->app, $request); // Direct calling

        $this->assertTrue($request::hasMacro($this->initializer::REQUEST_TIMESTAMP_MACRO));
        $this->assertTrue($request::hasMacro($this->initializer::REQUEST_ALLOCATED_MEMORY_MACRO));

        $this->assertInternalType('float', $time = $request::{$this->initializer::REQUEST_TIMESTAMP_MACRO}());
        $this->assertInternalType('integer', $mem = $request::{$this->initializer::REQUEST_ALLOCATED_MEMORY_MACRO}());

        \usleep(\random_int(100, 400));
        $closure($this->app, $request); // One more call

        $this->assertNotEquals($request::{$this->initializer::REQUEST_TIMESTAMP_MACRO}(), $time);
        $this->assertNotEquals($request::{$this->initializer::REQUEST_ALLOCATED_MEMORY_MACRO}(), $mem);
    }

    /**
     * @return void
     */
    public function testUpdateAppStatsWithPassingFalse()
    {
        $this->callMethod($this->initializer, 'initInjectStatsIntoRequest', [$this->callbacks, false]);
        $this->assertEmpty($this->callbacks->beforeHandleRequestStack());
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
