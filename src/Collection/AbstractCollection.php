<?php

namespace App\Collection;

use Iterator;
use ArrayIterator;

abstract class AbstractCollection
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
