<?php

namespace AvtoDev\RoadRunnerLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class AbstractTestCase extends BaseTestCase
{
    use Traits\CreatesApplicationTrait;

    /**
     * @return void
     */
    public function testFoo()
    {
        $this->assertTrue(true);
    }
}
