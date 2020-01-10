<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel;

use Throwable;
use Illuminate\Http\Request;
use Spiral\RoadRunner\PSR7Client;
use Spiral\Goridge\RelayInterface;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use AvtoDev\RoadRunnerLaravel\Handlers\HandlerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use AvtoDev\RoadRunnerLaravel\Handlers\RunAfterLoopContract as AfterLoop;
use AvtoDev\RoadRunnerLaravel\Handlers\RunBeforeLoopContract as BeforeLoop;
use AvtoDev\RoadRunnerLaravel\Handlers\RunAfterLoopIterationContract as AfterLoopIteration;
use AvtoDev\RoadRunnerLaravel\Handlers\RunAfterRequestHandleContract as AfterRequestHandle;
use AvtoDev\RoadRunnerLaravel\Handlers\RunBeforeLoopIterationContract as BeforeLoopIteration;
use AvtoDev\RoadRunnerLaravel\Handlers\RunBeforeRequestHandleContract as BeforeRequestHandle;

class Worker implements WorkerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string[]
     */
    protected static $allowed_handler_contracts = [
        BeforeLoop::class,
        BeforeLoopIteration::class,
        BeforeRequestHandle::class,
        AfterRequestHandle::class,
        AfterLoopIteration::class,
        AfterLoop::class,
    ];

    /**
     * @var array[]
     */
    protected $handlers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     */
    public function bootstrap(): void
    {
        /** @var HttpKernel $http_kernel */
        $http_kernel = $this->container->make(HttpKernel::class);

        /** @see \Illuminate\Foundation\Bootstrap\SetRequestForConsole Is required for illuminate kernel working */
        $this->container->instance('request', Request::create(
            '/', Request::METHOD_GET, [], [], [], $_SERVER
        ));

        $http_kernel->bootstrap();

        $this->container->register(ServiceProvider::class); // @todo: REMOVE! DEBUG ONLY !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        $this->bootstrap();

        /** @var EventsDispatcher $events */
        $events = $this->container->make(EventsDispatcher::class);

        $stream_relay = $this->createStreamRelay();
        $psr7_client  = $this->createPsr7Client($stream_relay);
        $http_factory  = $this->createHttpFactory();
        $diactoros     = $this->createDiactorosFactory();

        $handlers = $this->getHandlers($this->container->make(ConfigRepository::class), $this->container);

        /** @var BeforeLoop $handler */
        foreach ($handlers[BeforeLoop::class] ?? null as $handler) {
            $handler->handle($this);
        }

        while ($req = $psr7_client->acceptRequest()) {
            try {
                /** @var HttpKernel $http_kernel Bound as singleton in bootstrap file */
                $http_kernel  = $this->container->make(HttpKernel::class);
                $request      = Request::createFromBase($http_factory->createRequest($req));

                foreach ($handlers[BeforeRequestHandle::class] ?? null as $handler) {
                    $handler->handle($this, $request);
                }

                $response = $http_kernel->handle($request);

                foreach ($handlers[AfterRequestHandle::class] ?? null as $handler) {
                    $handler->handle($this, $request, $response);
                }

                $psr7_response = $diactoros->createResponse($response);
                $psr7_client->respond($psr7_response);
                $http_kernel->terminate($request, $response);

                foreach ($handlers[AfterLoopIteration::class] ?? null as $handler) {
                    $handler->handle($this, $request, $response);
                }

                unset($http_kernel);
            } catch (Throwable $e) {
                $psr7_client->getWorker()->error((string) $e);
            }
        }

        foreach ($handlers[AfterLoop::class] ?? null as $handler) {
            $handler->handle($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * @param ConfigRepository $config
     * @param Container        $container
     *
     * @return HandlerInterface[]
     */
    protected function getHandlers(ConfigRepository $config, Container $container): array
    {
        // Get handlers class names from configuration
        $handler_classes = \array_values(\array_filter(
            (array) $config->get(ServiceProvider::getConfigRootKeyName() . '.handlers'),
            static function ($value): bool {
                return \is_string($value) && \class_exists($value);
            }
        ));

        $result = [];

        // Loop over handler classes
        foreach ($handler_classes as $handler_class) {
            $handler = $container->make((string) $handler_class);

            if ($handler instanceof HandlerInterface) {
                foreach (static::$allowed_handler_contracts as $contract) {
                    if (\is_a($handler, $contract)) {
                        // Init array for handlers with contract
                        if (! \is_array($result[$contract] ?? null)) {
                            $result[$contract] = [];
                        }

                        // Push instance into them
                        $result[$contract][] =&$handler;
                    }
                }
            }
        }

        return $result;
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
