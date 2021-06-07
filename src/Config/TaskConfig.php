<?php

namespace App\Config;

class TaskConfig
{
    private const NEW_TASK_TITLE = 'New task!';
    private const NEW_TASK_DEFAULT_STATUS = TaskStatusConfig::PENDING_STATUS_ID;
    private const ROOT_TASK_DEFAULT_STATUS = TaskStatusConfig::NONE_STATUS_ID;
    private const ROOT_TASK_TITLE = "";

    private const MINIMUM_TRACKED_TIME = 60;

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

    /**
     * @return int
     */
    public function getRootTaskDefaultStatus(): int
    {
        return self::ROOT_TASK_DEFAULT_STATUS;
    }

    /**
     * @return string
     */
    public function getRootTaskTitle(): string
    {
        return self::ROOT_TASK_TITLE;
    }

    /**
     * @return int
     */
    public function getMinimumTrackedTime(): int
    {
        return self::MINIMUM_TRACKED_TIME;
    }

    /**
     * @return int
     */
    public function getActiveTaskStatus(): int
    {
        return TaskStatusConfig::IN_PROGRESS_STATUS_ID;
    }
}
