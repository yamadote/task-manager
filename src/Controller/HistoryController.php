<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Composer\HistoryResponseComposer;
use App\Repository\ActionRepository;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/internal-api/history")
 */
class HistoryController extends AbstractController
{
    private HistoryResponseComposer $historyResponseComposer;
    private ActionRepository $actionRepository;
    private TaskRepository $taskRepository;
    private JsonResponseBuilder $jsonResponseBuilder;

    public function __construct(
        HistoryResponseComposer $historyResponseComposer,
        ActionRepository $actionRepository,
        TaskRepository $taskRepository,
        JsonResponseBuilder $jsonResponseBuilder
    ) {
        $this->historyResponseComposer = $historyResponseComposer;
        $this->actionRepository = $actionRepository;
        $this->taskRepository = $taskRepository;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
    }

    /**
     * @Route("", name="app_api_history", methods={"GET"})
     */
    public function init(Request $request): JsonResponse
    {
        $taskId = $request->query->get('task');
        if (empty($taskId)) {
            $actions = $this->actionRepository->findByUser($this->getUser());
            return $this->historyResponseComposer->composeListResponse($this->getUser(), $actions, null);
        }
        $task = $this->taskRepository->find($taskId);
        if (empty($task)) {
            return $this->jsonResponseBuilder->buildError("Task not found");
        }
        if (!$task->getUser()->equals($this->getUser())) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $actions = $this->actionRepository->findByTask($task);
        return $this->historyResponseComposer->composeListResponse($this->getUser(), $actions, $task);
    }
}
