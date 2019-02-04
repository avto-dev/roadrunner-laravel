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
     */
    public function __construct(array $start_arguments = [], string $app_base_path = null)
    {
        $this->app_base_path = $app_base_path ?? $_ENV['APP_BASE_PATH'] ?? \dirname(__DIR__, 4);
        $this->callbacks     = new Callbacks;

        $this->app           = $this->createApplication($this->app_base_path);
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
        /** @var Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);

        while ($req = $this->psr7_client->acceptRequest()) {
            try {
                $this->callbacks->beforeLoopStack()->callEach($this->app, $req);

                $request = Request::createFromBase($this->http_factory->createRequest($req));

                $this->callbacks->beforeHandleRequestStack()->callEach($this->app, $request);

                $response = $kernel->handle($request);

                $this->callbacks->afterHandleRequestStack()->callEach($this->app, $request, $response);

                $psr7_response = $this->diactoros->createResponse($response);
                $this->psr7_client->respond($psr7_response);

                $kernel->terminate($request, $response);

                $this->callbacks->afterLoopStack()->callEach($this->app, $request, $response);
            } catch (Throwable $e) {
                $this->psr7_client->getWorker()->error($e->__toString());
            }
        }
    }

    /**
     * @param string $app_base_path
     *
     * @throws InvalidArgumentException
     *
     * @return Application
     */
    protected function createApplication(string $app_base_path): Application
    {
        if (\is_file($app_file = $app_base_path . '/bootstrap/app.php')) {
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
