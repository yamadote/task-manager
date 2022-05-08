<?php

namespace App\Service;

use App\Builder\TaskBuilder;
use App\Collection\TaskCollection;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;
use App\Repository\UserTaskSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private TaskStatusConfig $taskStatusConfig;
    private TaskRepository $taskRepository;
    private TrackedPeriodRepository $trackedPeriodRepository;
    private EntityManagerInterface $entityManager;
    private TaskBuilder $taskBuilder;
    private UserTaskSettingsRepository $userTaskSettingsRepository;

    public function __construct(
        TaskStatusConfig $taskStatusConfig,
        TaskRepository $taskRepository,
        TrackedPeriodRepository $trackedPeriodRepository,
        EntityManagerInterface $entityManager,
        TaskBuilder $taskBuilder,
        UserTaskSettingsRepository $userTaskSettingsRepository
    ) {
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskRepository = $taskRepository;
        $this->trackedPeriodRepository = $trackedPeriodRepository;
        $this->entityManager = $entityManager;
        $this->taskBuilder = $taskBuilder;
        $this->userTaskSettingsRepository = $userTaskSettingsRepository;
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

    /**
     * @param User $user
     * @param Task $parent
     * @return Task
     */
    public function createTask(User $user, Task $parent): Task
    {
        $task = $this->taskBuilder->buildNewTask($user, $parent);
        $this->entityManager->persist($task);

        $parentSettings = $this->userTaskSettingsRepository->findByUserAndTask($user, $parent);
        $parentSettings->setIsChildrenOpen(true);
        $this->entityManager->persist($parentSettings);

        $this->entityManager->flush();
        return $task;
    }
}