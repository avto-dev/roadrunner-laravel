<?php

namespace AvtoDev\RoadRunnerLaravel\Stacks;

use Countable;
use IteratorAggregate;
use Illuminate\Contracts\Support\Arrayable;

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
}
