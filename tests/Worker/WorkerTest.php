<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Worker;

use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Worker\Worker;
use AvtoDev\RoadRunnerLaravel\Worker\WorkerInterface;

class WorkerTest extends AbstractTestCase
{
    protected $vendor_laravel_path = __DIR__ . '/../../vendor/laravel/laravel';

    /**
     * @return void
     */
    public function testInterfacesAndTraits()
    {
        $this->assertInstanceOf(WorkerInterface::class, new Worker([], $this->vendor_laravel_path));
    }
}
