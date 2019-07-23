<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker;

use AvtoDev\RoadRunnerLaravel\Worker\Worker;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\WorkerInterface;
use AvtoDev\RoadRunnerLaravel\Worker\Callbacks\CallbacksInterface;

/**
 * @covers \AvtoDev\RoadRunnerLaravel\Worker\Worker
 */
class WorkerTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $vendor_laravel_path = __DIR__ . '/../../vendor/laravel/laravel';

    /**
     * @return void
     */
    public function testInterfacesAndTraits(): void
    {
        $this->assertInstanceOf(WorkerInterface::class, new Worker([], $this->vendor_laravel_path));
    }

    /**
     * @return void
     */
    public function testConstants(): void
    {
        $this->assertSame('APP_BASE_PATH', WorkerInterface::ENV_NAME_APP_BASE_PATH);
        $this->assertSame('APP_BOOTSTRAP_PATH', WorkerInterface::ENV_NAME_APP_BOOTSTRAP_PATH);
        $this->assertSame('APP_FORCE_HTTPS', WorkerInterface::ENV_NAME_APP_FORCE_HTTPS);
    }

    /**
     * @return void
     */
    public function testCreateWithCustomArguments(): void
    {
        $worker = new Worker(['--foo', '--not-reset', 'bar'], $this->vendor_laravel_path);

        $this->assertTrue($worker->startOptions()->getOption('foo'));
        $this->assertFalse($worker->startOptions()->getOption('reset'));
        $this->assertFalse($worker->startOptions()->hasOption('bar'));
    }

    /**
     * @return void
     */
    public function testGettersAsIs(): void
    {
        $worker = new Worker([], $this->vendor_laravel_path);

        $this->assertSame(\realpath($this->vendor_laravel_path), \realpath($worker->appBasePath()));
        $this->assertSame('/bootstrap/app.php', $worker->appBootstrapPath());
        $this->assertInstanceOf(Application::class, $worker->application());
        $this->assertEmpty($worker->startOptions()->getOptions());
        $this->assertInstanceOf(CallbacksInterface::class, $worker->callbacks());
    }

    /**
     * @return void
     */
    public function testGettersWithAppBasePathEnvValue(): void
    {
        $_ENV['APP_BASE_PATH'] = $this->vendor_laravel_path;

        $worker = new Worker;

        $this->assertSame(\realpath($this->vendor_laravel_path), \realpath($worker->appBasePath()));
    }

    /**
     * @return void
     */
    public function testGettersWithWrongAppBootstrapPathEnvValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $_ENV['APP_BOOTSTRAP_PATH'] = '/foo/bar';

        new Worker([], $this->vendor_laravel_path);
    }

    /**
     * @return void
     */
    public function testGettersWithWrongAppBootstrapPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Worker([], $this->vendor_laravel_path, '/foo/bar');
    }

    /**
     * @return void
     */
    public function testStart(): void
    {
        $this->expectException(\ErrorException::class);

        // This shit should not starts in tests ;)
        (new Worker([], $this->vendor_laravel_path))->start();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        unset($_ENV['APP_BOOTSTRAP_PATH'], $_ENV['APP_BASE_PATH']);

        parent::tearDown();
    }
}
