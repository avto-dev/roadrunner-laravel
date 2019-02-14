<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Redis\RedisManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Worker\WorkerInterface;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Redis\Connections\Connection as RedisConnection;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptionsInterface;

/**
 * @see \AvtoDev\RoadRunnerLaravel\Worker\Worker::start() - Look for callback parameters
 */
class CallbacksInitializer implements CallbacksInitializerInterface
{
    use Macroable;

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
     * @var CallbacksInterface
     */
    protected $callbacks;

    /**
     * {@inheritdoc}
     */
    public function __construct(StartOptionsInterface $start_options, CallbacksInterface $callbacks)
    {
        $this->start_options = $start_options;
        $this->callbacks     = $callbacks;
    }

    /**
     * {@inheritdoc}
     */
    public function makeInit()
    {
        // Initialize default callbacks
        $this->defaults($this->callbacks);

        // Iterate passed options and try to find initialization method for it
        foreach ($this->start_options->getOptions() as $option_name => $option_value) {
            $method = static::RULE_METHOD_PREFIX . Str::studly($option_name);

            if (\method_exists($this, $method)) {
                $this->$method($this->callbacks, $option_value);
            }
        }
    }

    /**
     * Default callbacks, that should be initialized without any conditions.
     *
     * @param CallbacksInterface $callbacks
     *
     * @return void
     */
    protected function defaults(CallbacksInterface $callbacks)
    {
        $callbacks->afterLoopIterationStack()
            ->push(function (Application $app, Request $request, Response $response) {
                \gc_collect_cycles(); // keep the memory low (this will slow down your application a bit)
            });

        $callbacks->beforeHandleRequestStack()
            ->push(function (Application $app, Request $request) {
                // Remove header 'HTTPS' (we keep control under this header manually)
                if ($request->headers->has(self::FORCE_HTTPS_HEADER_NAME)) {
                    $request->headers->remove(self::FORCE_HTTPS_HEADER_NAME);
                }
            });

        $env = WorkerInterface::ENV_NAME_APP_FORCE_HTTPS;

        if (((bool) ($_ENV[$env] ?? env($env, false))) === true) {
            $callbacks->beforeHandleRequestStack()
                ->push(function (Application $app, Request $request) {
                    $request->headers->set(self::FORCE_HTTPS_HEADER_NAME, 'HTTPS');
                });
        }
    }

    /**
     * For option: "--force-https".
     *
     * @see \AvtoDev\RoadRunnerLaravel\ServiceProvider::boot()
     *
     * @param CallbacksInterface $callbacks
     * @param bool|mixed         $value
     *
     * @return void
     */
    protected function initForceHttps(CallbacksInterface $callbacks, $value)
    {
        $callbacks->beforeHandleRequestStack()
            ->push(function (Application $app, Request $request) use ($value) {
                // Attach special header for telling application "force use https schema!"
                // IMPORTANT! 'FORCE-HTTPS' header can be set externally
                if ($value === true || $request->headers->has(self::FORCE_HTTPS_EXTERNAL_HEADER_NAME)) {
                    $request->headers->set(self::FORCE_HTTPS_HEADER_NAME, 'HTTPS');
                }
            });
    }

    /**
     * For option "--reset-debug-info"
     *
     * @param CallbacksInterface $callbacks
     * @param bool|mixed         $value
     *
     * @return void
     */
    protected function initResetDebugInfo(CallbacksInterface $callbacks, $value)
    {
        if ($value === true) {
            $callbacks->beforeLoopIterationStack()
                ->push(function (Application $app) {
                    $_SERVER['LARAVEL_START_TIME']   = microtime(true);
                    $_SERVER['LARAVEL_START_MEMORY'] = memory_get_usage();
                });
        }
    }

    /**
     * For option: "--reset-db-connections".
     *
     * @param CallbacksInterface $callbacks
     * @param bool|mixed         $value
     *
     * @return void
     */
    protected function initResetDbConnections(CallbacksInterface $callbacks, $value)
    {
        if ($value === true) {
            $callbacks->afterLoopIterationStack()
                ->push(function (Application $app, Request $request, Response $response) {
                    // Drop database connections
                    if (($db_manager = $app->make('db')) instanceof DatabaseManager) {
                        if (\is_array($db_connections = $db_manager->getConnections())) {
                            foreach ($db_connections as $db_connection) {
                                /** @var DatabaseConnection $db_connection */
                                if (\method_exists($db_connection, 'disconnect')) {
                                    $db_connection->disconnect();
                                }
                            }
                        }
                    }
                });
        }
    }

    /**
     * For option: "--reset-redis-connections".
     *
     * @param CallbacksInterface $callbacks
     * @param bool|mixed         $value
     *
     * @return void
     */
    protected function initResetRedisConnections(CallbacksInterface $callbacks, $value)
    {
        if ($value === true) {
            $callbacks->afterLoopIterationStack()
                ->push(function (Application $app, Request $request, Response $response) {
                    // Drop redis connections
                    if (($redis_manager = $app->make('redis')) instanceof RedisManager) {
                        if (\method_exists($redis_manager, 'connections')) {
                            $redis_connections = $redis_manager->connections();
                            if (\is_array($redis_connections)) {
                                foreach ($redis_connections as $redis_connection) {
                                    /** @var RedisConnection $redis_connection */
                                    if (\method_exists($redis_connection, 'disconnect')) {
                                        $redis_connection->disconnect();
                                    }
                                }
                            }
                        }
                    }
                });
        }
    }
}
