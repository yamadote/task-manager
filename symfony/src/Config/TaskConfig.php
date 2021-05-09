<?php

namespace App\Config;

class TaskConfig
{
    private const NEW_TASK_TITLE = 'New task!';
    private const NEW_TASK_DEFAULT_STATUS = TaskStatusConfig::PENDING_STATUS_ID;

    /**
     * @return string
     */
    public function getNewTaskTitle(): string
    {
        return self::NEW_TASK_TITLE;
    }

    /**
     * @return int
     */
    public function getNewTaskDefaultStatus(): int
    {
        return self::NEW_TASK_DEFAULT_STATUS;
    }
}
