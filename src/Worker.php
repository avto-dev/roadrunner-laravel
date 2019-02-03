<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerWorkerLaravel;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Spiral\RoadRunner\PSR7Client;
use Spiral\Goridge\RelayInterface;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerWorkerLaravel\Settings\Settings;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use AvtoDev\RoadRunnerWorkerLaravel\Settings\SettingsInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;

class Worker
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
     * @var SettingsInterface
     */
    protected $settings;

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
     * Worker constructor.
     *
     * @param string|null $app_base_path
     */
    public function __construct(string $app_base_path = null)
    {
        $this->app_base_path = $app_base_path ?? $_ENV['APP_BASE_PATH'] ?? \dirname(__DIR__, 4);

        $this->app          = $this->appFactory($app_base_path);
        $this->settings     = $this->settingsFactory();
        $this->stream_relay = $this->streamRelayFactory();
        $this->psr7_client  = $this->psr7ClientFactory($this->stream_relay);
        $this->http_factory = $this->httpFactoryFactory();
        $this->diactoros    = $this->diactorosFactoryFactory();
    }

    /**
     * Set application instance.
     *
     * @param Application $app
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
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
     * Set settings instance.
     *
     * @param SettingsInterface $settings
     */
    public function setSettings(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get the settings instance.
     *
     * @return SettingsInterface
     */
    public function settings(): SettingsInterface
    {
        return $this->settings;
    }

    /**
     * @return string
     */
    public function getAppBasePath(): string
    {
        return $this->app_base_path;
    }

    /**
     * @return void
     */
    public function start()
    {
        /** @var Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);

        while ($req = $this->psr7_client->acceptRequest()) {
            try {
                $request = Request::createFromBase($this->http_factory->createRequest($req));

                $response = $kernel->handle($request);

                $psr7_response = $this->diactoros->createResponse($response);
                $this->psr7_client->respond($psr7_response);

                $kernel->terminate($request, $response);
            } catch (\Throwable $e) {
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
    protected function appFactory(string $app_base_path): Application
    {
        if (\is_file($app_file = $app_base_path . '/bootstrap/app.php')) {
            return require $app_file;
        }

        throw new InvalidArgumentException("Application bootstrap file [$app_file] was not found");
    }

    /**
     * @return SettingsInterface
     */
    protected function settingsFactory(): SettingsInterface
    {
        global $argv;

        return new Settings($argv);
    }

    /**
     * @return RelayInterface
     */
    protected function streamRelayFactory(): RelayInterface
    {
        return new \Spiral\Goridge\StreamRelay(STDIN, STDOUT);
    }

    /**
     * @param RelayInterface $stream_relay
     *
     * @return PSR7Client
     */
    protected function psr7ClientFactory(RelayInterface $stream_relay)
    {
        return new PSR7Client(new \Spiral\RoadRunner\Worker($stream_relay));
    }

    /**
     * @return HttpFoundationFactoryInterface
     */
    protected function httpFactoryFactory(): HttpFoundationFactoryInterface
    {
        return new HttpFoundationFactory;
    }

    /**
     * @return HttpMessageFactoryInterface
     */
    protected function diactorosFactoryFactory(): HttpMessageFactoryInterface
    {
        return new DiactorosFactory;
    }
}
