<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Builder\TaskResponseBuilder;
use App\Collection\TaskCollection;
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

    public function composeListResponse(User $user, TaskCollection $tasks): JsonResponse
    {
        $root = $this->findRootTask($user, $tasks);
        $settings = $this->settingsRepository->findByTasks($tasks);
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($user);
        $statusCollection = $this->taskStatusConfig->getStatusCollection();
        $activeTask = null;
        if ($activePeriod) {
            $path = $this->taskRepository->getTaskPath($activePeriod->getTask());
            $activeTask = $this->taskResponseBuilder->buildActiveTaskResponse($activePeriod, $path);
        }
        return $this->jsonResponseBuilder->build([
            'statuses' => $this->taskResponseBuilder->buildStatusListResponse($statusCollection),
            'tasks' => $this->taskResponseBuilder->buildTaskListResponse($tasks, $settings, $root),
            'activeTask' => $activeTask
        ]);
    }

    public function composeTaskResponse(User $user, Task $task, UserTaskSettings $settings): JsonResponse
    {
        $root = $this->taskRepository->findUserRootTask($user);
        return $this->taskResponseBuilder->buildTaskJsonResponse($task, $settings, $root);
    }

    private function findRootTask(User $user, TaskCollection $tasks): Task
    {
        foreach ($tasks->getIterator() as $task) {
            if ($task->getParent() === null) {
                return $task;
            }
        }
        return $this->taskRepository->findUserRootTask($user);
    }
}
