<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel;

use RuntimeException;
use Spiral\RoadRunner\PSR7Client;
use Spiral\Goridge\RelayInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;

class Worker implements WorkerInterface
{
    /**
     * @var Container
     */
    protected $container;

    protected $handlers = [
        'before.loop'           => [],
        'before.loop.iteration' => [],
        'before.request.handle' => [],
        'after.request.handle'  => [],
        'after.loop.iteration'  => [],
        'after.loop'            => [],
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;

        dump($this->createApplication());
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        // @todo: Implement start() method.
    }

    /**
     * @throws RuntimeException If bootstrap file was not found
     *
     * @return Application
     */
    protected function createApplication(): Application
    {
        $path = $this->container->bootstrapPath('app.php');

        if (! \is_file($path)) {
            throw new RuntimeException("Application bootstrap file [$path] was not found");
        }

        return require $path;
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
     * @todo Do NOT use deprecated factory class
     */
    protected function createDiactorosFactory(): HttpMessageFactoryInterface
    {
        return new \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
    }
}
