<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Worker;

use Throwable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spiral\RoadRunner\PSR7Client;
use Spiral\Goridge\RelayInterface;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\Callbacks;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;
use AvtoDev\RoadRunnerLaravel\Worker\StartOptions\StartOptionsInterface;
use AvtoDev\RoadRunnerLaravel\Worker\CallbacksInitializer\CallbacksInitializerInterface;

class Worker implements WorkerInterface
{
    /**
     * @var string
     */
    protected $app_base_path;

    /**
     * @var string
     */
    protected $app_bootstrap_path;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var StartOptionsInterface
     */
    protected $start_options;

    /**
     * @var RelayInterface
     */
    protected $stream_relay;

    /**
     * @var PSR7Client
     */
    protected $psr7_client;

    /**
     * @var HttpFoundationFactoryInterface
     */
    protected $http_factory;

    /**
     * @var HttpMessageFactoryInterface
     */
    protected $diactoros;

    /**
     * @var CallbacksInterface
     */
    protected $callbacks;

    /**
     * Worker constructor.
     *
     * @param array       $start_arguments
     * @param string|null $app_base_path
     * @param string|null $app_bootstrap_path
     */
    public function __construct(array $start_arguments = [],
                                string $app_base_path = null,
                                string $app_bootstrap_path = null)
    {
        $this->app_base_path      = $app_base_path ?? $this->getDefaultAppBasePath();
        $this->app_bootstrap_path = DIRECTORY_SEPARATOR . \ltrim(
                $app_bootstrap_path ?? $this->getDefaultAppBootstrapPath(), '\\/ '
            );

        $this->callbacks     = new Callbacks;
        $this->app           = $this->createApplication($this->app_base_path, $this->app_bootstrap_path);
        $this->start_options = $this->createStartOptions($start_arguments);
        $this->stream_relay  = $this->createStreamRelay();
        $this->psr7_client   = $this->createPsr7Client($this->stream_relay);
        $this->http_factory  = $this->createHttpFactory();
        $this->diactoros     = $this->createDiactorosFactory();

        $initializer = $this->createCallbacksInitializer($this->start_options, $this->callbacks);

        // Initialize callbacks, based on start options
        $initializer->makeInit();
    }

    /**
     * Get the application instance.
     *
     * @return Application
     */
    public function application(): Application
    {
        return $this->app;
    }

    /**
     * Get the start options instance.
     *
     * @return StartOptionsInterface
     */
    public function startOptions(): StartOptionsInterface
    {
        return $this->start_options;
    }

    /**
     * @return string
     */
    public function appBasePath(): string
    {
        return $this->app_base_path;
    }

    /**
     * @return string
     */
    public function appBootstrapPath(): string
    {
        return $this->app_bootstrap_path;
    }

    /**
     * @return CallbacksInterface
     */
    public function callbacks(): CallbacksInterface
    {
        return $this->callbacks;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $refresh_app = $this->start_options->hasOption('refresh-app')
                       && $this->start_options->getOption('refresh-app') === true;

        $this->callbacks->beforeLoopStarts()->callEach($this->app);

        /** @var Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);

        while ($req = $this->psr7_client->acceptRequest()) {
            try {
                $this->callbacks->beforeLoopIterationStack()->callEach($this->app, $req);

                $request = Request::createFromBase($this->http_factory->createRequest($req));

                $this->callbacks->beforeHandleRequestStack()->callEach($this->app, $request);

                $response = $kernel->handle($request);

                $this->callbacks->afterHandleRequestStack()->callEach($this->app, $request, $response);

                $psr7_response = $this->diactoros->createResponse($response);
                $this->psr7_client->respond($psr7_response);

                $kernel->terminate($request, $response);

                $this->callbacks->afterLoopIterationStack()->callEach($this->app, $request, $response);

                if ($refresh_app === true) {
                    unset($kernel, $this->app);
                    $this->app = $this->createApplication($this->app_base_path, $this->app_bootstrap_path);
                    $kernel    = $this->app->make(Kernel::class);
                }
            } catch (Throwable $e) {
                $this->psr7_client->getWorker()->error($e->__toString());
            }
        }

        $this->callbacks->afterLoopEnds()->callEach($this->app);
    }

    /**
     * Get default application base path.
     *
     * @return string
     */
    protected function getDefaultAppBasePath(): string
    {
        return env(static::ENV_NAME_APP_BASE_PATH) ?? $_ENV[static::ENV_NAME_APP_BASE_PATH] ?? \dirname(__DIR__, 5);
    }

    /**
     * Get default application bootstrap file path.
     *
     * @return string
     */
    protected function getDefaultAppBootstrapPath(): string
    {
        return env(static::ENV_NAME_APP_BOOTSTRAP_PATH) ?? $_ENV[static::ENV_NAME_APP_BOOTSTRAP_PATH] ?? '/bootstrap/app.php';
    }

    /**
     * @param string $app_base_path
     * @param string $app_bootstrap_path
     *
     * @throws InvalidArgumentException
     *
     * @return Application
     */
    protected function createApplication(string $app_base_path, string $app_bootstrap_path): Application
    {
        if (\is_file($app_file = $app_base_path . $app_bootstrap_path)) {
            return require $app_file;
        }

        throw new InvalidArgumentException("Application bootstrap file [$app_file] was not found");
    }

    /**
     * @param array $start_options
     *
     * @return StartOptionsInterface
     */
    protected function createStartOptions(array $start_options): StartOptionsInterface
    {
        return new StartOptions\StartOptions($start_options);
    }

    /**
     * @param resource|mixed $in
     * @param resource|mixed $out
     *
     * @return RelayInterface
     */
    protected function createStreamRelay($in = \STDIN, $out = \STDOUT): RelayInterface
    {
        return new \Spiral\Goridge\StreamRelay($in, $out);
    }

    /**
     * @param RelayInterface $stream_relay
     *
     * @return PSR7Client
     */
    protected function createPsr7Client(RelayInterface $stream_relay)
    {
        return new PSR7Client(new \Spiral\RoadRunner\Worker($stream_relay));
    }

    /**
     * @return HttpFoundationFactoryInterface
     */
    protected function createHttpFactory(): HttpFoundationFactoryInterface
    {
        return new \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
    }

    /**
     * @return HttpMessageFactoryInterface
     */
    protected function createDiactorosFactory(): HttpMessageFactoryInterface
    {
        return new \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
    }

    /**
     * @param StartOptionsInterface $start_options
     * @param CallbacksInterface    $callback_stacks
     *
     * @return CallbacksInitializerInterface
     */
    protected function createCallbacksInitializer(StartOptionsInterface $start_options,
                                                  CallbacksInterface $callback_stacks): CallbacksInitializerInterface
    {
        return new CallbacksInitializer\CallbacksInitializer($start_options, $callback_stacks);
    }
}
