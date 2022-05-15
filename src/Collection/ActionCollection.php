<?php

namespace App\Collection;

use App\Entity\Action;
use Iterator;

/**
 * Class TaskCollection
 * @package App\Collection
 * @method Action[]|Iterator getIterator()
 */
class ActionCollection extends AbstractCollection
{
    /** @var Action[] */
    protected array $list;

    public function add(Action $task): void
    {
        $this->list[] = $task;
    }
}
