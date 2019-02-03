<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Support\Stacks;

class CallbacksStack extends AbstractStack
{
    /**
     * Stack of callable items.
     *
     * @var array|callable[]
     */
    protected $items = [];

    /**
     * Push callable item own into stack.
     *
     * @see <https://secure.php.net/manual/ru/function.is-callable.php>
     *
     * @param array|callable $callable
     *
     * @return $this
     */
    public function push($callable)
    {
        if (\is_callable($callable, false)) {
            $this->items[] = $callable;
        }

        return $this;
    }

    /**
     * Call each of stack closures with passed arguments.
     *
     * @param mixed ...$arguments
     *
     * @return $this
     */
    public function callEach(...$arguments)
    {
        foreach ($this->items as $item) {
            $item(...$arguments);
        }

        return $this;
    }
}
