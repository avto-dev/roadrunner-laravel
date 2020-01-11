<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel;

use RuntimeException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spiral\RoadRunner\PSR7Client;
use Spiral\Goridge\RelayInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Bootstrap\SetRequestForConsole;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopStoppedEvent;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use AvtoDev\RoadRunnerLaravel\Events\BeforeLoopStartedEvent;
use AvtoDev\RoadRunnerLaravel\Events\AfterLoopIterationEvent;
use AvtoDev\RoadRunnerLaravel\Events\BeforeLoopIterationEvent;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use AvtoDev\RoadRunnerLaravel\Events\AfterRequestHandlingEvent;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use AvtoDev\RoadRunnerLaravel\Events\BeforeRequestHandlingEvent;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

/**
 * Idea is taken from the package: https://github.com/swooletw/laravel-swoole.
 */
class Worker implements WorkerInterface
{
    /**
     * Laravel application base path.
     *
     * @var string
     */
    protected $base_path;

    /**
     * Create a new Worker instance.
     *
     * @param string $base_path Laravel application base path
     */
    public function __construct(string $base_path)
    {
        $this->base_path = $base_path;
    }

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        $app = $this->createApplication($this->base_path);

        $this->bootstrapApplication($app);

        $psr7_client  = $this->createPsr7Client($this->createStreamRelay());
        $http_factory = $this->createHttpFactory();
        $diactoros    = $this->createDiactorosFactory();

        $this->fireEvent($app, new BeforeLoopStartedEvent($app));

        while ($req = $psr7_client->acceptRequest()) {
            $sandbox = clone $app;

            $this->setApplicationInstance($sandbox);

            /** @var HttpKernelContract $http_kernel */
            $http_kernel = $sandbox->make(HttpKernelContract::class);

            try {
                $this->fireEvent($sandbox, new BeforeLoopIterationEvent($sandbox, $req));
                $request = Request::createFromBase($http_factory->createRequest($req));

                $this->fireEvent($sandbox, new BeforeRequestHandlingEvent($sandbox, $request));
                $response = $http_kernel->handle($request);
                $this->fireEvent($sandbox, new AfterRequestHandlingEvent($sandbox, $request, $response));

                $psr7_response = $diactoros->createResponse($response);
                $psr7_client->respond($psr7_response);
                $http_kernel->terminate($request, $response);

                $this->fireEvent($sandbox, new AfterLoopIterationEvent($sandbox, $request, $response));
            } catch (\Throwable $e) {
                $psr7_client->getWorker()->error((string) $e);
            } finally {
                unset($http_kernel, $response, $request, $sandbox);

                $this->setApplicationInstance($app);
            }
        }

        $this->fireEvent($app, new AfterLoopStoppedEvent($app));
    }

    /**
     * @param ApplicationContract $app
     *
     * @return void
     */
    protected function setApplicationInstance(ApplicationContract $app): void
    {
        $app->instance('app', $app);
        $app->instance(Container::class, $app);

        Container::setInstance($app);

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }

    /**
     * Create the new application instance.
     *
     * @param string $base_path
     *
     * @throws InvalidArgumentException
     *
     * @return ApplicationContract
     */
    protected function createApplication(string $base_path): ApplicationContract
    {
        $path = \implode(\DIRECTORY_SEPARATOR, [\rtrim($base_path, \DIRECTORY_SEPARATOR), 'bootstrap', 'app.php']);

        if (! \is_file($path)) {
            throw new InvalidArgumentException("Application bootstrap file was not found in [{$path}]");
        }

        return require $path;
    }

    /**
     * Bootstrap passed application.
     *
     * @param ApplicationContract $app
     *
     * @throws RuntimeException
     *
     * @return void
     */
    protected function bootstrapApplication(ApplicationContract $app): void
    {
        /** @var \Illuminate\Foundation\Http\Kernel $http_kernel */
        $http_kernel = $app->make(HttpKernelContract::class);

        $bootstrappers = $this->getKernelBootstrappers($http_kernel);

        // Insert `SetRequestForConsole` bootstrapper before `RegisterProviders` if it does not exists
        if (! \in_array(SetRequestForConsole::class, $bootstrappers, true)) {
            $register_index = \array_search(RegisterProviders::class, $bootstrappers, true);

            if ($register_index !== false) {
                \array_splice($bootstrappers, $register_index, 0, [SetRequestForConsole::class]);
            }
        }

        // Method `bootstrapWith` declared in interface `\Illuminate\Contracts\Foundation\Application` since
        // `illuminate/contracts:v5.8` - https://git.io/JvfOq
        if (\method_exists($app, $boot_method = 'bootstrapWith')) {
            $app->{$boot_method}($bootstrappers);
        } else {
            throw new RuntimeException("Required method [{$boot_method}] does not exists on application instance");
        }

        /** @var ConfigRepository $config */
        $config = $app->make(ConfigRepository::class);

        // Pre-resolve instances
        foreach ((array) $config->get(ServiceProvider::getConfigRootKey() . '.pre_resolving', []) as $abstract) {
            if (\is_string($abstract) && $app->bound($abstract)) {
                $app->make($abstract);
            }
        }
    }

    /**
     * Get HTTP or Console kernel bootstrappers.
     *
     * @param \Illuminate\Foundation\Http\Kernel|\Illuminate\Foundation\Console\Kernel $kernel
     *
     * @return string[] Bootstrappers class names
     */
    protected function getKernelBootstrappers($kernel): array
    {
        ($method = (new \ReflectionObject($kernel))->getMethod($name = 'bootstrappers'))->setAccessible(true);

        return (array) $method->invoke($kernel);
    }

    /**
     * @param ApplicationContract $app
     * @param object              $event
     *
     * @return void
     */
    protected function fireEvent(ApplicationContract $app, $event): void
    {
        /** @var EventsDispatcher $events */
        $events = $app->make(EventsDispatcher::class);

        if ($events->hasListeners(\get_class($event))) {
            $events->dispatch($event);
        }
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
