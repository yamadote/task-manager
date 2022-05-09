<?php

namespace App\Checker;

use App\Entity\Task;
use App\Entity\User;

class TaskPermissionChecker
{
    public function canEditTask(User $user, Task $task): bool
    {
        return $this->check($user, $task);
    }

    public function canDeleteTask(User $user, Task $task): bool
    {
        return $this->check($user, $task);
    }

    public function canTrackTask(User $user, Task $task): bool
    {
        return $this->check($user, $task);
    }

    private function check(User $user, Task $task): bool
    {
        return $user->equals($task->getUser()) && null !== $task->getParent();
    }
}