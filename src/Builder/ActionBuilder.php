<?php

namespace App\Builder;

use App\Entity\Action;
use App\Entity\Task;
use App\Entity\User;
use DateTime;

class ActionBuilder
{
    public function buildAction(User $user, ?Task $task, string $type, string $message): Action
    {
        $action = new Action();
        $action->setUser($user);
        $action->setTask($task);
        $action->setCreatedAt(new DateTime());
        $action->setType($type);
        $action->setMessage($message);
        return $action;
    }
}