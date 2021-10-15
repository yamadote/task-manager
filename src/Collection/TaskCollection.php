<?php

namespace App\Collection;

use App\Entity\Task;
use Iterator;

/**
 * Class TaskCollection
 * @package App\Collection
 * @method Task[]|Iterator getIterator()
 */
class TaskCollection extends AbstractCollection
{
    /** @var Task[] */
    protected array $list;

    public function add(Task $task): void
    {
        $this->list[] = $task;
    }

    public function has(Task $task): bool
    {
        foreach ($this->list as $item) {
            if ($item->equals($task)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        $ids = [];
        foreach ($this->list as $task) {
            $ids[] = $task->getId();
        }
        return $ids;
    }
}
