<?php

namespace App\Collection;

use App\Entity\Task;
use App\Entity\UserTaskSettings;

class UserTaskSettingsCollection extends AbstractCollection
{
    /** @var UserTaskSettings[] */
    protected array $list;

    public function findOneByTask(Task $task): ?UserTaskSettings
    {
        foreach ($this->list as $settings) {
            if ($task->equals($settings->getTask())) {
                return $settings;
            }
        }
        return null;
    }
}
