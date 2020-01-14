<?php

namespace AvtoDev\RoadRunnerLaravel\Support\Stacks;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

interface StackInterface extends Countable, Arrayable, IteratorAggregate
{
    /**
     * Push item into stack.
     *
     * @param mixed $item
     */
    public function push($item);

    /**
     * Get all stack items.
     *
     * @return array
     */
    public function all();

    /**
     * Make stack clearing.
     */
    public function clear();

    /**
     * Get first element from stack.
     *
     * @return mixed
     */
    public function first();
}
