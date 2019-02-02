<?php

namespace AvtoDev\RoadRunnerWorkerLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use AvtoDev\RoadRunnerWorkerLaravel\LaravelPackageServiceProvider;

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

    /**
     * @return void
     */
    public function testFoo()
    {
        $this->assertTrue(true);
    }
}
