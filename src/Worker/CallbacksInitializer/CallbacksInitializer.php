<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Redis\RedisManager;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Worker\CallbackStacks;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Redis\Connections\Connection as RedisConnection;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptionsInterface;

/**
 * @see \AvtoDev\RoadRunnerLaravel\Worker\Worker::start() - Look for callback parameters
 */
class CallbacksInitializer implements CallbacksInitializerInterface
{
    /**
     * "Magic" method prefix. You can simply add your own method, name it as an required option name this this prefix.
     *
     * See for examples below.
     */
    const RULE_METHOD_PREFIX = 'init';

    /**
     * @var StartOptionsInterface
     */
    protected $start_options;

    /**
     * @var CallbackStacks
     */
    protected $callback_stacks;

    /**
     * {@inheritdoc}
     */
    public function __construct(StartOptionsInterface $start_options, CallbackStacks $callback_stacks)
    {
        $this->start_options   = $start_options;
        $this->callback_stacks = $callback_stacks;

        // Initialize default callbacks
        $this->defaults();

        // Iterate passed options and try to find initialization method for it
        foreach ($start_options->getOptions() as $option_name => $option_value) {
            $method = static::RULE_METHOD_PREFIX . Str::studly($option_name);

            if (\method_exists($this, $method)) {
                $this->$method($option_value);
            }
        }
    }

    /**
     * Default callbacks, that should be initialized without any conditions.
     *
     * @return void
     */
    protected function defaults()
    {
        $this->callback_stacks->afterLoopStack()
            ->push(function (Application $app, Request $request, Response $response) {
                \gc_collect_cycles(); // keep the memory low (this will slow down your application a bit)
            });

        $this->callback_stacks->beforeHandleRequestStack()
            ->push(function (Application $app, Request $request) {
                // Remove header 'HTTPS' (we keep control under this header manually)
                if ($request->headers->has(self::FORCE_HTTPS_HEADER_NAME)) {
                    $request->headers->remove(self::FORCE_HTTPS_HEADER_NAME);
                }
            });
    }

    /**
     * For option: "force-https".
     *
     * @param mixed $value
     *
     * @return void
     */
    protected function initForceHttps($value)
    {
        $this->callback_stacks->beforeHandleRequestStack()
            ->push(function (Application $app, Request $request) use ($value) {
                // Attach special header for telling application "force use https schema!"
                // IMPORTANT! 'FORCE-HTTPS' header can be set externally
                if ($value === true || $request->headers->has('FORCE-HTTPS')) {
                    $request->headers->set(self::FORCE_HTTPS_HEADER_NAME, 'HTTPS');
                }
            });
    }

    /**
     * For option: "reset-connections".
     *
     * @param mixed $value
     *
     * @return void
     */
    protected function initResetConnections($value)
    {
        if ($value === true) {
            $this->callback_stacks->afterLoopStack()
                ->push(function (Application $app, Request $request, Response $response) {
                    // Drop database connections
                    $db_manager = $app->make('db');
                    if ($db_manager instanceof DatabaseManager) {
                        $db_connections = $db_manager->getConnections();
                        if (\is_iterable($db_connections)) {
                            foreach ($db_connections as $connection) {
                                if ($connection instanceof DatabaseConnection) {
                                    $connection->disconnect();
                                }

                                unset($connection);
                            }
                        }
                    }

                    // Drop redis connections
                    $redis_manager = $app->make('redis');
                    if ($redis_manager instanceof RedisManager) {
                        $redis_connections = $redis_manager->connections();
                        if (\is_iterable($redis_connections)) {
                            foreach ($redis_connections as $connection) {
                                if ($connection instanceof RedisConnection && \method_exists($connection,
                                        'disconnect')) {
                                    $connection->disconnect();
                                }

                                unset($connection);
                            }
                        }
                    }
                });
        }
    }
}
