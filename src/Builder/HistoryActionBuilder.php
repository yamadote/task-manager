<?php

namespace App\Builder;

use App\Entity\HistoryAction;
use App\Entity\Task;
use App\Entity\User;
use DateTime;

class HistoryActionBuilder
{
    public function buildAction(User $user, ?Task $task, string $type, string $message): HistoryAction
    {
        $action = new HistoryAction();
        $action->setUser($user);
        $action->setTask($task);
        $action->setCreatedAt(new DateTime());
        $action->setType($type);
        $action->setMessage($message);
        return $action;
    }
}