<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Builder\TaskResponseBuilder;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;
use App\Repository\UserTaskSettingsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\UserTaskSettings;

class TaskResponseComposer
{
    private TaskResponseBuilder $taskResponseBuilder;
    private TaskRepository $taskRepository;
    private UserTaskSettingsRepository $settingsRepository;
    private TrackedPeriodRepository $trackedPeriodRepository;
    private TaskStatusConfig $taskStatusConfig;
    private JsonResponseBuilder $jsonResponseBuilder;

    public function __construct(
        TaskResponseBuilder $taskResponseBuilder,
        TaskRepository $taskRepository,
        UserTaskSettingsRepository $settingsRepository,
        TrackedPeriodRepository $trackedPeriodRepository,
        TaskStatusConfig $taskStatusConfig,
        JsonResponseBuilder $jsonResponseBuilder
    ) {
        $this->taskResponseBuilder = $taskResponseBuilder;
        $this->taskRepository = $taskRepository;
        $this->settingsRepository = $settingsRepository;
        $this->trackedPeriodRepository = $trackedPeriodRepository;
        $this->taskStatusConfig = $taskStatusConfig;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
    }

    /**
     * @param User $user
     * @param Task[] $tasks
     * @return JsonResponse
     */
    public function composeListResponse(User $user, array $tasks): JsonResponse
    {
        $root = $this->findRootTask($user, $tasks);
        $settings = $this->settingsRepository->findByTasks($tasks);
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($user);
        $statusList = $this->taskStatusConfig->getStatusList();
        $activeTask = null;
        if ($activePeriod) {
            $activeTask = $this->taskResponseBuilder->buildActiveTaskResponse($activePeriod);
        }
        return $this->jsonResponseBuilder->build([
            'statuses' => $this->taskResponseBuilder->buildStatusListResponse($statusList),
            'tasks' => $this->taskResponseBuilder->buildTaskListResponse($tasks, $settings, $root),
            'activeTask' => $activeTask
        ]);
    }

    public function composeTaskResponse(User $user, Task $task, UserTaskSettings $settings): JsonResponse
    {
        $root = $this->taskRepository->findUserRootTask($user);
        return $this->taskResponseBuilder->buildTaskJsonResponse($task, $settings, $root);
    }

    /**
     * @param User $user
     * @param Task[] $tasks
     * @return Task
     */
    private function findRootTask(User $user, array $tasks): Task
    {
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                return $task;
            }
        }
        return $this->taskRepository->findUserRootTask($user);
    }
}
