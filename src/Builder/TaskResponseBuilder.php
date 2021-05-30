<?php

namespace App\Builder;

use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\UserTaskSettings;
use App\Repository\UserTaskSettingsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilder
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    /** @var UserTaskSettingsRepository */
    private $settingsRepository;

    /** @var UserTaskSettingsBuilder */
    private $settingsBuilder;

    /**
     * TaskResponseBuilder constructor.
     * @param TaskStatusConfig $taskStatusConfig
     */
    public function __construct(
        TaskStatusConfig  $taskStatusConfig,
        UserTaskSettingsRepository $settingsRepository,
        UserTaskSettingsBuilder $settingsBuilder
    ) {
        $this->taskStatusConfig = $taskStatusConfig;
        $this->settingsRepository = $settingsRepository;
        $this->settingsBuilder = $settingsBuilder;
    }

    /**
     * @param User $user
     * @param iterable $tasks
     * @param Task $root
     * @return JsonResponse
     */
    public function buildListResponse(User $user, iterable $tasks, Task $root): JsonResponse
    {
        $settings = $this->settingsRepository->findByTasks($tasks);
        $tasksResponse = [];
        /** @var Task $task */
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                continue;
            }
            $setting = $settings[$task->getId()] ?? $this->settingsBuilder->buildDefaultSettings($user, $task);
            $tasksResponse[] = $this->buildTaskArrayResponse($task, $setting, $root);
        }
        $statusesResponse = [];
        foreach ($this->taskStatusConfig->getStatusList() as $status) {
            $statusesResponse[] = [
                'id' => $status->getId(),
                'title' => $status->getTitle(),
                'color' => $status->getColor()
            ];
        }
        return new JsonResponse([
            'statuses' => $statusesResponse,
            'tasks' => $tasksResponse
        ]);
    }

    /**
     * @param Task $task
     * @param UserTaskSettingsBuilder $userSettings
     * @param Task $root
     * @return JsonResponse
     */
    public function buildTaskResponse(Task $task, UserTaskSettings $userSettings, Task $root): JsonResponse
    {
        return new JsonResponse($this->buildTaskArrayResponse($task, $userSettings, $root));
    }

    /**
     * @param Task $task
     * @param UserTaskSettingsBuilder $userSettings
     * @param Task $root
     * @return array
     */
    private function buildTaskArrayResponse(Task $task, UserTaskSettings $userSettings, Task $root): array
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

    /**
     * @param Task $task
     * @param Task $root
     * @return int|null
     */
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
}
