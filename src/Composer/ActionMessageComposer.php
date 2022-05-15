<?php

namespace App\Composer;

use App\Config\TaskStatusConfig;
use DateTimeInterface;

class ActionMessageComposer
{
    private TaskStatusConfig $taskStatusConfig;

    public function __construct(TaskStatusConfig $taskStatusConfig)
    {
        $this->taskStatusConfig = $taskStatusConfig;
    }

    public function composeNewTaskMessage(): string
    {
        return 'Created new task';
    }

    public function composeTaskTitleUpdateMessage(string $title): string
    {
        return "The task title changed to \"$title\"";
    }

    public function composeTaskLinkUpdateMessage(string $link): string
    {
        if (empty($link)) {
            return "The task link removed";
        }
        return "The task link set to \"$link\"";
    }

    public function composeTaskReminderUpdateMessage(?DateTimeInterface $reminder): string
    {
        if (null === $reminder) {
            return "The task reminder removed";
        }
        $date = $reminder->format("Y/m/d H:i");
        return "The task reminder set to $date (UTC)";
    }

    public function composeTaskStatusUpdateMessage(int $status): string
    {
        $statusTitle = $this->taskStatusConfig->getStatusById($status)->getTitle();
        return "The task status changed to \"$statusTitle\"";
    }

    public function composeTaskDescriptionUpdateMessage(?string $description): string
    {
        if (empty($description)) {
            return "The task description removed";
        }
        return "The task description updated";
    }
}