<?php

namespace App\Collection;

use App\Entity\HistoryAction;
use Iterator;

/**
 * Class TaskCollection
 * @package App\Collection
 * @method HistoryAction[]|Iterator getIterator()
 */
class HistoryActionCollection extends AbstractCollection
{
    /** @var HistoryAction[] */
    protected array $list;

    public function add(HistoryAction $task): void
    {
        $this->list[] = $task;
    }
}
