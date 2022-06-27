<?php

namespace App\Composer;

use App\Config\TaskStatusConfig;
use DateTimeInterface;

class HistoryActionMessageComposer
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
        return 'The task title changed to [title]' . $title . '[/title]';
    }

    public function composeTaskLinkUpdateMessage(string $link): string
    {
        if (empty($link)) {
            return 'The task link removed';
        }
        return 'The task link set to [link]' . $link . '[/link]';
    }

    public function composeTaskReminderUpdateMessage(?DateTimeInterface $reminder): string
    {
        if (null === $reminder) {
            return 'The task reminder removed';
        }
        return 'The task reminder set to [reminder]' . $reminder->getTimestamp() . '[/reminder]';
    }

    public function composeTaskStatusUpdateMessage(int $id): string
    {
        $status = $this->taskStatusConfig->getStatusById($id);
        $slug = $status->getSlug();
        $title = $status->getTitle();
        return 'The task status changed to [status slug="' . $slug . '"]' . $title . '[/status]';
    }

    public function composeTaskDescriptionUpdateMessage(?string $description): string
    {
        if (empty($description)) {
            return 'The task description removed';
        }
        return 'The task description updated';
    }
}