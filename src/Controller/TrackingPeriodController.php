<?php

namespace App\Controller;

use App\Config\TaskConfig;
use App\Entity\Task;
use App\Entity\TrackedPeriod;
use App\Repository\TrackedPeriodRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("internal-api/tasks")
 * @IsGranted("ROLE_USER")
 */
class TrackingPeriodController extends AbstractController
{
    /** @var TrackedPeriodRepository */
    private $trackedPeriodRepository;

    /** @var TaskConfig */
    private $taskConfig;

    public function __construct(
        TrackedPeriodRepository $trackedPeriodRepository,
        TaskConfig $taskConfig
    ) {
        $this->trackedPeriodRepository = $trackedPeriodRepository;
        $this->taskConfig = $taskConfig;
    }

    /**
     * @Route("/{id}/start", name="app_task_start", methods={"POST"})
     */
    public function start(Task $task): JsonResponse
    {
        if (!$this->canTrackTask($task)) {
            // todo: error message
            return new JsonResponse();
        }
        // todo: check if small diff reactivate period
        $lastPeriod = $this->trackedPeriodRepository->findLastTrackedPeriod($this->getUser());
        if (!is_null($lastPeriod) && $lastPeriod->isActive()) {
            if ($lastPeriod->getTask()->equals($task)) {
                // todo: add error message: you can't start active task
                return new JsonResponse();
            }
            $this->finishPeriod($lastPeriod);
            $lastPeriod = $this->trackedPeriodRepository->findLastTrackedPeriod($this->getUser());
        }
        $entityManager = $this->getDoctrine()->getManager();
        // todo: check case when removed active period is the last
        if (!is_null($lastPeriod) && $this->canContinuePeriod($task, $lastPeriod)) {
            $lastPeriod->setFinishedAt(null);
            $entityManager->flush();
            // todo: success message
            return new JsonResponse();
        }
        $period = new TrackedPeriod();
        $period->setUser($this->getUser());
        $period->setStartedAt(new DateTime());
        $period->setTask($task);
        $entityManager->persist($period);
        $entityManager->flush();
        // todo: success message
        return new JsonResponse();
    }

    /**
     * @Route("/{id}/finish", name="app_task_finish", methods={"POST"})
     */
    public function finish(Task $task): JsonResponse
    {
        $activePeriod = $this->trackedPeriodRepository->getActivePeriod($this->getUser());
        if (is_null($activePeriod) || !$activePeriod->getTask()->equals($task)) {
            // todo: error message
            return new JsonResponse();
        }
        $this->finishPeriod($activePeriod);
        // todo: success message
        return new JsonResponse();
    }

    /**
     * @param TrackedPeriod $period
     */
    private function finishPeriod(TrackedPeriod $period): void
    {
        $entityManager = $this->getDoctrine()->getManager();
        if (!$this->hasMinimumTrackedTime($period)) {
            $entityManager->remove($period);
        } else {
            $period->setFinishedAt(new DateTime());
        }
        $entityManager->flush();
    }

    /**
     * @param Task $task
     * @return bool
     */
    private function canTrackTask(Task $task): bool
    {
        return $this->getUser()->equals($task->getUser()) && null !== $task->getParent();
    }

    /**
     * @param TrackedPeriod $period
     * @return bool
     */
    private function hasMinimumTrackedTime(TrackedPeriod $period): bool
    {
        $finishedTime = $period->getFinishedAt() ?? new DateTime();
        $diff = $finishedTime->getTimestamp() - $period->getStartedAt()->getTimestamp();
        return $diff < $this->taskConfig->getMinimumTrackedTime();
    }

    /**
     * @param TrackedPeriod $period
     * @return bool
     */
    private function canContinuePeriod(Task $task, TrackedPeriod $period): bool
    {
        if ($period->getFinishedAt() === null) {
            return false;
        }
        if (!$task->equals($period->getTask())) {
            return false;
        }
        $diff = (new DateTime())->getTimestamp() - $period->getFinishedAt()->getTimestamp();
        return $diff < $this->taskConfig->getMinimumTrackedTime();
    }
}
