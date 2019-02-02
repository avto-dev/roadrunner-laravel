<?php

namespace AvtoDev\RoadRunnerWorkerLaravel\Tests;

use AvtoDev\RoadRunnerWorkerLaravel\LaravelPackageServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class AbstractTestCase extends BaseTestCase
{
    use Traits\CreatesApplicationTrait;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->app->register(LaravelPackageServiceProvider::class);
    }
}
