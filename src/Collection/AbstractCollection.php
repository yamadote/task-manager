<?php

namespace App\Collection;

use Iterator;
use ArrayIterator;
use IteratorAggregate;

abstract class AbstractCollection implements IteratorAggregate
{
    protected array $list;

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->list);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->list;
    }
}
