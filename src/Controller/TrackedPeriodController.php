<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Checker\TaskPermissionChecker;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;
use App\Service\TrackedPeriodService;
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
    private TaskPermissionChecker $taskPermissionChecker;
    private TrackedPeriodService $trackedPeriodService;

    public function __construct(
        TrackedPeriodRepository $trackedPeriodRepository,
        TaskRepository $taskRepository,
        JsonResponseBuilder $jsonResponseBuilder,
        TaskPermissionChecker $taskPermissionChecker,
        TrackedPeriodService $trackedPeriodService
    ) {
        $this->trackedPeriodRepository = $trackedPeriodRepository;
        $this->taskRepository = $taskRepository;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
        $this->taskPermissionChecker = $taskPermissionChecker;
        $this->trackedPeriodService = $trackedPeriodService;
    }

    /**
     * @Route("/{id}/start", name="app_task_start", methods={"POST"})
     */
    public function start(Task $task): JsonResponse
    {
        if (!$this->taskPermissionChecker->canTrackTask($this->getUser(), $task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $lastPeriod = $this->trackedPeriodRepository->findLastTrackedPeriod($this->getUser());
        if (!is_null($lastPeriod) && $lastPeriod->isActive()) {
            if ($lastPeriod->getTask()->equals($task)) {
                return $this->jsonResponseBuilder->buildError("Task already active");
            }
            $this->trackedPeriodService->finishPeriod($lastPeriod);
        }
        $this->trackedPeriodService->startPeriod($this->getUser(), $task);
        $path = $this->taskRepository->getTaskPath($task);
        return $this->jsonResponseBuilder->build(['activeTask' => ['path' => $path->getIds()]]);
    }

    /**
     * @Route("/{id}/finish", name="app_task_finish", methods={"POST"})
     */
    public function finish(Task $task): JsonResponse
    {
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($this->getUser());
        if (is_null($activePeriod) || !$activePeriod->getTask()->equals($task)) {
            return $this->jsonResponseBuilder->buildError("The task can't be finished");
        }
        $this->trackedPeriodService->finishPeriod($activePeriod);
        return $this->jsonResponseBuilder->build();
    }
}
