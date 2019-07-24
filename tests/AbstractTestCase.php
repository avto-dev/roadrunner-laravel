<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use PHPUnit\Framework\ExpectationFailedException;
use SuperClosure\Serializer as ClosureSerializer;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        // $app->useStoragePath(...);
        // $app->loadEnvironmentFrom(...);

        $app->make(Kernel::class)->bootstrap();

        $app->register(ServiceProvider::class);

        return $app;
    }

    /**
     * Asserts that passed class uses expected traits.
     *
     * @param string          $class
     * @param string|string[] $expected_traits
     * @param string          $message
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function assertClassUsesTraits($class, $expected_traits, string $message = ''): void
    {
        /**
         * Returns all traits used by a trait and its traits.
         *
         * @param string $trait
         *
         * @return string[]
         */
        $trait_uses_recursive = function ($trait) use (&$trait_uses_recursive) {
            $traits = \class_uses($trait);

            foreach ($traits as $trait_iterate) {
                $traits += $trait_uses_recursive($trait_iterate);
            }

            return $traits;
        };

        /**
         * Returns all traits used by a class, its subclasses and trait of their traits.
         *
         * @param object|string $class
         *
         * @return array
         */
        $class_uses_recursive = function ($class) use ($trait_uses_recursive) {
            if (\is_object($class)) {
                $class = \get_class($class);
            }

            $results = [];

            foreach (\array_reverse(\class_parents($class)) + [$class => $class] as $class_iterate) {
                $results += $trait_uses_recursive($class_iterate);
            }

            return \array_values(\array_unique((array) $results));
        };

        $uses = $class_uses_recursive($class);

        foreach ((array) $expected_traits as $trait_class) {
            $this->assertContains($trait_class, $uses, $message === ''
                ? 'Class does not uses passed traits'
                : $message);
        }
    }

    /**
     * Calls a instance method (public/private/protected) by its name.
     *
     * @param object $object
     * @param string $method_name
     * @param array  $args
     *
     * @throws ReflectionException
     *
     * @return mixed
     *
     * @deprecated
     */
    public function callMethod($object, string $method_name, array $args = [])
    {
        $class  = new ReflectionClass($object);
        $method = $class->getMethod($method_name);

        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }

    /**
     * Calculate closure hash sum.
     *
     * As you know - you cannot serialize closure 'as is' for hashing. So - this is a little hack for this shit!
     *
     * @param Closure $closure
     *
     * @throws Exception
     *
     * @return string
     */
    public function getClosureHash(Closure $closure): string
    {
        // @codeCoverageIgnoreStart
        if (! class_exists(ClosureSerializer::class)) {
            throw new Exception(\sprintf(
                'Package [%s] is required for [%s] method',
                'jeremeamia/superclosure',
                __METHOD__
            ));
        }

        // @codeCoverageIgnoreEnd

        return sha1(
            (new ClosureSerializer)->serialize($closure->bindTo(new \stdClass))
        );
    }
}
