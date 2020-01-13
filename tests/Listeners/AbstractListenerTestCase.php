<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Listeners;

use ReflectionObject;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;
use AvtoDev\RoadRunnerLaravel\Listeners\ListenerInterface;

abstract class AbstractListenerTestCase extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testImplementation(): void
    {
        $this->assertInstanceOf(ListenerInterface::class, $this->listenerFactory());
    }

    /**
     * Listener factory.
     *
     * @return ListenerInterface|mixed
     */
    abstract protected function listenerFactory();

    /**
     * Test listener `handle` method.
     *
     * @return void
     */
    abstract protected function testHandle(): void;

    /**
     * @param object $object
     * @param string $property
     *
     * @return mixed
     */
    protected function getProperty($object, string $property)
    {
        $property = (new ReflectionObject($object))->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     *
     * @return void
     */
    protected function setProperty($object, string $property, $value): void
    {
        $property = (new ReflectionObject($object))->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
