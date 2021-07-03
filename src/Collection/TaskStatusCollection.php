<?php

namespace App\Collection;

use App\Entity\TaskStatus;

class TaskStatusCollection extends AbstractCollection
{
    /** @var TaskStatus[] */
    protected array $list;

    public function getIds(): array
    {
        return array_map(static function (TaskStatus $taskStatus) {
            return $taskStatus->getId();
        }, $this->list);
    }
}
