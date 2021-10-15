<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Entity\Task;
use App\Entity\TrackedPeriod;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("internal-api/tasks")
 * @IsGranted("ROLE_USER")
 */
class TrackedPeriodController extends AbstractController
{
    private TrackedPeriodRepository $trackedPeriodRepository;
    private TaskRepository $taskRepository;
    private JsonResponseBuilder $jsonResponseBuilder;

    public function __construct(
        TrackedPeriodRepository $trackedPeriodRepository,
        TaskRepository $taskRepository,
        JsonResponseBuilder $jsonResponseBuilder
    ) {
        $this->trackedPeriodRepository = $trackedPeriodRepository;
        $this->taskRepository = $taskRepository;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
    }

    /**
     * @Route("/{id}/start", name="app_task_start", methods={"POST"})
     */
    public function start(Task $task): JsonResponse
    {
        if (!$this->canTrackTask($task)) {
            // todo: error message
            return $this->jsonResponseBuilder->build();
        }
        // todo: check if small diff reactivate period
        $lastPeriod = $this->trackedPeriodRepository->findLastTrackedPeriod($this->getUser());
        if (!is_null($lastPeriod) && $lastPeriod->isActive()) {
            if ($lastPeriod->getTask()->equals($task)) {
                // todo: add error message: you can't start active task
                return $this->jsonResponseBuilder->build();
            }
            $this->finishPeriod($lastPeriod);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $period = new TrackedPeriod();
        $period->setUser($this->getUser());
        $startedAt = new DateTime();
        $period->setStartedAt($startedAt);
        $period->setTask($task);
        $entityManager->persist($period);
        $entityManager->flush();
        $path = $this->taskRepository->getTaskPath($task);
        return $this->jsonResponseBuilder->build([
            'activeTask' => ['path' => $path->getIds()]
        ]);
    }

    /**
     * @Route("/{id}/finish", name="app_task_finish", methods={"POST"})
     */
    public function finish(Task $task): JsonResponse
    {
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($this->getUser());
        if (is_null($activePeriod) || !$activePeriod->getTask()->equals($task)) {
            // todo: error message
            return $this->jsonResponseBuilder->build();
        }
        $this->finishPeriod($activePeriod);
        $this->getDoctrine()->getManager()->flush();
        // todo: success message
        return $this->jsonResponseBuilder->build();
    }

    private function finishPeriod(TrackedPeriod $period): void
    {
        // todo: remove if period has minimum tracked time
        $finishedAt = new DateTime();
        $period->setFinishedAt($finishedAt);
        $task = $period->getTask();
        $diff = $finishedAt->getTimestamp() - $period->getStartedAt()->getTimestamp();
        $this->taskRepository->increaseTrackedTime($task, $diff);
    }

    private function canTrackTask(Task $task): bool
    {
        return $this->getUser()->equals($task->getUser()) && null !== $task->getParent();
    }
}
