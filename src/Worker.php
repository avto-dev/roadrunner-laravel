<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel;

use Throwable;
use Illuminate\Http\Request;
use Spiral\RoadRunner\PSR7Client;
use Spiral\Goridge\RelayInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopStoppedEvent;
use AvtoDev\RoadRunnerLaravel\Events\BeforeLoopStartedEvent;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use AvtoDev\RoadRunnerLaravel\Events\BeforeLoopIterationEvent;
use AvtoDev\RoadRunnerLaravel\Events\AfterRequestHandlingEvent;
use AvtoDev\RoadRunnerLaravel\Events\BeforeRequestHandlingEvent;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;

class Worker implements WorkerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Create a new Worker instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(): void
    {
        /** @var HttpKernel $http_kernel */
        $http_kernel = $this->container->make(HttpKernel::class);

        // THis action is required for correct illuminate kernel working
        /** @see \Illuminate\Foundation\Bootstrap\SetRequestForConsole */
        $this->container->instance('request', Request::create('/', Request::METHOD_GET, [], [], [], $_SERVER));

        $http_kernel->bootstrap();

        $this->container->register(ServiceProvider::class); // @todo: REMOVE! DEBUG ONLY !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        /** @var ConfigRepository $config */
        $config = $this->container->make(ConfigRepository::class);

        // Pre-resolve instances
        foreach ((array) $config->get(ServiceProvider::getConfigRootKeyName() . '.pre_resolving', []) as $abstract) {
            if (\is_string($abstract) && $this->container->bound($abstract)) {
                $this->container->make($abstract);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        $this->bootstrap();

        $psr7_client  = $this->createPsr7Client($this->createStreamRelay());
        $http_factory = $this->createHttpFactory();
        $diactoros    = $this->createDiactorosFactory();

        $this->fireEvent(new BeforeLoopStartedEvent($this));

        while ($req = $psr7_client->acceptRequest()) {
            try {
                /** @var HttpKernel $http_kernel */
                $http_kernel = $this->container->make(HttpKernel::class);

                $this->fireEvent(new BeforeLoopIterationEvent($this, $req));
                $request = Request::createFromBase($http_factory->createRequest($req));

                $this->fireEvent(new BeforeRequestHandlingEvent($this, $request));
                $response = $http_kernel->handle($request);
                $this->fireEvent(new AfterRequestHandlingEvent($this, $request, $response));

                $psr7_response = $diactoros->createResponse($response);
                $psr7_client->respond($psr7_response);
                $http_kernel->terminate($request, $response);

                $this->fireEvent(new AfterLoopIterationEvent($this, $request, $response));

                unset($http_kernel, $request, $response);
            } catch (Throwable $e) {
                $psr7_client->getWorker()->error((string) $e);
            }
        }

        $this->fireEvent(new AfterLoopStoppedEvent($this));
    }

    /**
     * @param string|object $event
     *
     * @return void
     */
    protected function fireEvent($event): void
    {
        /** @var EventsDispatcher $events Always extract actual events dispatcher instance */
        $events = $this->container->make(EventsDispatcher::class);

        $events->dispatch($event);
    }

    /**
     * @param resource|mixed $in  Must be readable
     * @param resource|mixed $out Must be writable
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
     * @return PSR7Client|mixed
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
     *
     * @todo Do NOT use deprecated factory class
     */
    protected function createDiactorosFactory(): HttpMessageFactoryInterface
    {
        return new \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
    }
}
