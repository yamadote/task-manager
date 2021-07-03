<?php

namespace App\Builder;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\UserTaskSettings;

class UserTaskSettingsBuilder
{
    public function buildDefaultSettings(Task $task): UserTaskSettings
    {
        $settings = new UserTaskSettings();
        $settings->setUser($task->getUser());
        $settings->setTask($task);
        $settings->setIsAdditionalPanelOpen(false);
        $settings->setIsChildrenOpen(false);
        return $settings;
    }
}
