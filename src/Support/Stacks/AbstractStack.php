<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Support\Stacks;

use ArrayIterator;
use Illuminate\Support\Traits\Macroable;

abstract class AbstractStack implements StackInterface
{
    use Macroable;

    /**
     * The items contained in the stack.
     *
     * @var array
     */
    protected $items = [];

    /**
     * AbstractStack constructor.
     *
     * @param array $callable_items
     */
    public function __construct(array $callable_items = [])
    {
        foreach ($callable_items as $callable_item) {
            $this->push($callable_item);
        }
    }

    /**
     * Create a new stack instance.
     *
     * @param mixed ...$arguments
     *
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function clear()
    {
        $this->items = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IteratorAggregate
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->all();
    }
}
