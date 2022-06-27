<?php

namespace App\Composer;

use App\Builder\HistoryActionResponseBuilder;
use App\Builder\JsonResponseBuilder;
use App\Collection\HistoryActionCollection;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class HistoryResponseComposer
{
    private TaskRepository $taskRepository;
    private JsonResponseBuilder $jsonResponseBuilder;
    private HistoryActionResponseBuilder $historyActionResponseBuilder;

    public function __construct(
        TaskRepository $taskRepository,
        JsonResponseBuilder $jsonResponseBuilder,
        HistoryActionResponseBuilder $historyActionResponseBuilder
    ) {
        $this->taskRepository = $taskRepository;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
        $this->historyActionResponseBuilder = $historyActionResponseBuilder;
    }

    public function composeListResponse(User $user, HistoryActionCollection $actions, ?Task $task): JsonResponse
    {
        $includeActionTask = $task === null;
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        return $this->jsonResponseBuilder->build([
            'actions' => $this->historyActionResponseBuilder->buildActionListResponse($actions, $includeActionTask),
            'reminderNumber' => $reminderNumber,
            'task' => $task ? $this->composeTaskResponse($task) : null
        ]);
    }

    private function composeTaskResponse(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle()
        ];
    }
}
