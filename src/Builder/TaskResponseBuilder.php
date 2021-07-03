<?php

namespace App\Builder;

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

    /**
     * @param TaskStatus[] $statusList
     * @return array
     */
    public function buildStatusListResponse(array $statusList): array
    {
        $statusListResponse = [];
        foreach ($statusList as $status) {
            $statusListResponse[] = $this->buildStatusResponse($status);
        }
        return $statusListResponse;
    }

    /**
     * @param Task[] $tasks
     * @param UserTaskSettings[] $settings
     * @param Task $root
     * @return array
     */
    public function buildTaskListResponse(array $tasks, array $settings, Task $root): array
    {
        $taskListResponse = [];
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                continue;
            }
            $setting = $this->findTaskSetting($task, $settings);
            $taskListResponse[] = $this->buildTaskResponse($task, $setting, $root);
        }
        return $taskListResponse;
    }

    /**
     * @param Task $task
     * @param UserTaskSettings[] $settings
     * @return UserTaskSettings
     */
    private function findTaskSetting(Task $task, array $settings): UserTaskSettings
    {
        if (array_key_exists($task->getId(), $settings)) {
            return $settings[$task->getId()];
        }
        return $this->settingsBuilder->buildDefaultSettings($task);
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
