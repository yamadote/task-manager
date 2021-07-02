<?php

namespace App\Builder;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\UserTaskSettings;

class UserTaskSettingsBuilder
{
    public function buildDefaultSettings(User $user, Task $task): UserTaskSettings
    {
        $settings = new UserTaskSettings();
        $settings->setUser($user);
        $settings->setTask($task);
        $settings->setIsAdditionalPanelOpen(false);
        $settings->setIsChildrenOpen(false);
        return $settings;
    }
}
