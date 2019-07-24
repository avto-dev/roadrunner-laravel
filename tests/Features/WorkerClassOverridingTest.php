<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Features;

use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;

/**
 * @coversNothing
 */
class WorkerClassOverridingTest extends AbstractTestCase
{
    private $path = __DIR__ . '/worker_works';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        if (\file_exists($this->path)) {
            $this->assertTrue(\unlink($this->path));
        }

        \putenv('RR_WORKER_CLASS=' . CustomWorker::class);
        \putenv('APP_BASE_PATH=' . \realpath(__DIR__ . '/../../vendor/laravel/laravel'));

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // unset
        \putenv('RR_WORKER_CLASS');
        \putenv('APP_BASE_PATH');

        if (\file_exists($this->path)) {
            $this->assertTrue(\unlink($this->path));
        }
    }

    /**
     * @see CustomWorker::start()
     *
     * @return void
     */
    public function testWorkerClassOverriding(): void
    {
        $output = [];
        $return_var = null;

        $this->assertFileNotExists($this->path);

        \exec(PHP_BINARY . ' ' . \realpath(__DIR__ . '/../../bin/rr-worker'), $output, $return_var);

        $this->assertFileExists($this->path);
        $this->assertStringEqualsFile($this->path, 'Hell yeah!');
    }
}
