<?php

namespace App\Service;

use App\Collection\TaskCollection;
use App\Config\TaskStatusConfig;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;

class TaskService
{
    private TaskStatusConfig $taskStatusConfig;
    private TaskRepository $taskRepository;
    private TrackedPeriodRepository $trackedPeriodRepository;

    public function __construct(
        TaskStatusConfig $taskStatusConfig,
        TaskRepository $taskRepository,
        TrackedPeriodRepository $trackedPeriodRepository
    ) {
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskRepository = $taskRepository;
        $this->trackedPeriodRepository = $trackedPeriodRepository;
    }

    public function getTasksByStatus(User $user, string $statusSlug): TaskCollection
    {
        $status = $this->taskStatusConfig->getStatusBySlug($statusSlug);
        $isProgressStatus = $status->getId() === TaskStatusConfig::IN_PROGRESS_STATUS_ID;
        $fullHierarchy = !$isProgressStatus;

        $tasks = $this->taskRepository->findUserTasksByStatus($user, $status, $fullHierarchy);
        if ($isProgressStatus) {
            $tasks = $this->addActiveTask($tasks, $user);
        }
        return $tasks;
    }

    private function addActiveTask(TaskCollection $tasks, User $user): TaskCollection
    {
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($user);
        if (null === $activePeriod) {
            return $tasks;
        }
        $activeTask = $activePeriod->getTask();
        if (!$tasks->has($activeTask)) {
            $tasks->add($activeTask);
        }
        return $tasks;
    }
}