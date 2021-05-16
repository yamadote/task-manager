<?php

namespace App\Controller;

use App\Config\TaskConfig;
use App\Entity\Task;
use App\Entity\TrackedPeriod;
use App\Repository\TrackedPeriodRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 * TODO: add user permission
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
     * @Route("/{id}/start", name="app_task_start", methods={"GET","POST"})
     */
    public function start(Request $request, Task $task): Response
    {
        if (!$this->canTrackTask($task)) {
            return $this->redirectBack($request);
        }
        // todo: check if small diff reactivate period
        $lastPeriod = $this->trackedPeriodRepository->findLastTrackedPeriod($this->getUser());
        if (!is_null($lastPeriod) && $lastPeriod->isActive()) {
            if ($lastPeriod->getTask()->equals($task)) {
                // todo: add error message: you can't start active task
                return $this->redirectBack($request);
            }
            $this->finishPeriod($lastPeriod);
        }
        $entityManager = $this->getDoctrine()->getManager();
        // todo: check case when removed active period is the last
        if (!is_null($lastPeriod) && $this->canContinuePeriod($task, $lastPeriod)) {
            $lastPeriod->setFinishedAt(null);
            $entityManager->flush();
            return $this->redirectBack($request);
        }
        $period = new TrackedPeriod();
        $period->setUser($this->getUser());
        $period->setStartedAt(new DateTime());
        $period->setTask($task);
        $entityManager->persist($period);
        $entityManager->flush();
        return $this->redirectBack($request);
    }

    /**
     * @Route("/{id}/finish", name="app_task_finish", methods={"GET","POST"})
     */
    public function finish(Request $request, Task $task): Response
    {
        $activePeriod = $this->trackedPeriodRepository->getActivePeriod($this->getUser());
        if (is_null($activePeriod) || !$activePeriod->getTask()->equals($task)) {
            return $this->redirectBack($request);
        }
        $this->finishPeriod($activePeriod);
        return $this->redirectBack($request);
    }

    /**
     * @param TrackedPeriod $period
     * todo: move to repository
     */
    private function finishPeriod(TrackedPeriod $period): void
    {
//        if ($this->isPeriodSmall($period)) {
//            // todo: remove period
//        } else {
            $period->setFinishedAt(new DateTime());
//        }
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param Task $task
     * @return bool
     */
    private function canTrackTask(Task $task): bool
    {
        return $this->getUser()->equals($task->getUser()) && null !== $task->getParent();
    }

//    /**
//     * @param TrackedPeriod $period
//     * @return bool
//     */
//    private function isPeriodSmall(TrackedPeriod $period): bool
//    {
//        $finishedTime = $period->getFinishedAt() ?? new DateTime();
//        $diff = $finishedTime->getTimestamp() - $period->getStartedAt()->getTimestamp();
//        return $diff < $this->taskConfig->getMinimumTrackedTime();
//    }

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
