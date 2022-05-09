<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TrackedPeriod;
use App\Entity\User;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class TrackedPeriodService
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    public function startPeriod(User $user, Task $task): void
    {
        $period = new TrackedPeriod();
        $period->setUser($user);
        $startedAt = new DateTime();
        $period->setStartedAt($startedAt);
        $period->setTask($task);
        $this->entityManager->persist($period);
        $this->entityManager->flush();
    }

    public function finishPeriod(TrackedPeriod $period): void
    {
        $finishedAt = new DateTime();
        $period->setFinishedAt($finishedAt);
        $task = $period->getTask();
        $diff = $finishedAt->getTimestamp() - $period->getStartedAt()->getTimestamp();
        $this->taskRepository->increaseTrackedTime($task, $diff);
        $this->entityManager->flush();
    }
}