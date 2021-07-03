<?php

namespace App\Builder;

use App\Collection\TaskCollection;
use App\Collection\TaskStatusCollection;
use App\Collection\UserTaskSettingsCollection;
use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Entity\TrackedPeriod;
use App\Entity\UserTaskSettings;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilder
{
    private UserTaskSettingsBuilder $settingsBuilder;
    private JsonResponseBuilder $jsonResponseBuilder;

    public function __construct(
        UserTaskSettingsBuilder $settingsBuilder,
        JsonResponseBuilder $jsonResponseBuilder
    ) {
        $this->settingsBuilder = $settingsBuilder;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
    }

    public function buildStatusListResponse(TaskStatusCollection $collection): array
    {
        $statusListResponse = [];
        foreach ($collection->getIterator() as $status) {
            $statusListResponse[] = $this->buildStatusResponse($status);
        }
        return $statusListResponse;
    }

    public function buildTaskListResponse(
        TaskCollection $tasks,
        UserTaskSettingsCollection $settings,
        Task $root
    ): array {
        $taskListResponse = [];
        foreach ($tasks->getIterator() as $task) {
            if ($task->getParent() === null) {
                continue;
            }
            $setting = $settings->findOneByTask($task) ?? $this->settingsBuilder->buildDefaultSettings($task);
            $taskListResponse[] = $this->buildTaskResponse($task, $setting, $root);
        }
        return $taskListResponse;
    }

    public function buildTaskJsonResponse(Task $task, UserTaskSettings $userSettings, Task $root): JsonResponse
    {
        return $this->jsonResponseBuilder->build($this->buildTaskResponse($task, $userSettings, $root));
    }

    private function buildTaskResponse(Task $task, UserTaskSettings $userSettings, Task $root): array
    {
        $reminder = $task->getReminder();
        $createdAt = $task->getCreatedAt();
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'parent' => $this->getParentId($task, $root),
            'link' => $task->getLink(),
            'reminder' => $reminder ? $reminder->getTimestamp() : null,
            'createdAt' => $createdAt ? $createdAt->getTimestamp() : null,
            'status' => $task->getStatus(),
            'isAdditionalPanelOpen' => $userSettings->getIsAdditionalPanelOpen(),
            'isChildrenOpen' => $userSettings->getIsChildrenOpen()
        ];
    }

    private function getParentId(Task $task, Task $root): ?int
    {
        if (null === $task->getParent()) {
            return null;
        }
        if ($task->getParent()->equals($root)) {
            return null;
        }
        return $task->getParent()->getId();
    }

    private function buildStatusResponse(TaskStatus $status): array
    {
        return [
            'id' => $status->getId(),
            'title' => $status->getTitle(),
            'color' => $status->getColor()
        ];
    }

    public function buildActiveTaskResponse(TrackedPeriod $activePeriod): array
    {
        return [
            'task' => $activePeriod->getTask()->getId(),
            'startedAt' => $activePeriod->getStartedAt()->getTimestamp()
        ];
    }
}
