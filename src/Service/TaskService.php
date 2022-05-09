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
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

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
            $activePeriod = $this->trackedPeriodRepository->findActivePeriod($user);
            if ($activePeriod) {
                $activeTask = $activePeriod->getTask();
                if (!$tasks->has($activeTask)) {
                    $tasks->add($activeTask);
                }
            }
        }
        return $tasks;
    }

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

    public function editTask(Task $task, ParameterBag $input): void
    {
        if ($input->has('title')) {
            $task->setTitle($input->get('title'));
        }
        if ($input->has('link')) {
            $task->setLink($input->get('link'));
        }
        if ($input->has('reminder')) {
            $reminder = $input->get('reminder');
            $task->setReminder($reminder ? (new DateTime())->setTimestamp($reminder) : null);
        }
        if ($input->has('status')) {
            $task->setStatus($input->get('status'));
        }
        if ($input->has('description')) {
            $task->setDescription($input->get('description'));
        }
        $this->entityManager->flush();
    }

    public function editTaskSettings(User $user, Task $task, ParameterBag $input): void
    {
        $setting = $this->userTaskSettingsRepository->findByUserAndTask($user, $task);
        if ($input->has('isChildrenOpen')) {
            $setting->setIsChildrenOpen($input->get('isChildrenOpen'));
        }
        if ($input->has('isAdditionalPanelOpen')) {
            $setting->setIsAdditionalPanelOpen($input->get('isAdditionalPanelOpen'));
        }
        $this->entityManager->persist($setting);
        $this->entityManager->flush();
    }

    public function deleteTask(Task $task): void
    {
        $children = $this->taskRepository->findChildren($task);
        foreach ($children as $child) {
            $this->entityManager->remove($child);
        }
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}