<?php

namespace App\Composer;

use App\Builder\ActionResponseBuilder;
use App\Builder\JsonResponseBuilder;
use App\Collection\ActionCollection;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class HistoryResponseComposer
{
    private TaskRepository $taskRepository;
    private JsonResponseBuilder $jsonResponseBuilder;
    private ActionResponseBuilder $actionResponseBuilder;

    public function __construct(
        TaskRepository $taskRepository,
        JsonResponseBuilder $jsonResponseBuilder,
        ActionResponseBuilder $actionResponseBuilder
    ) {
        $this->taskRepository = $taskRepository;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
        $this->actionResponseBuilder = $actionResponseBuilder;
    }

    public function composeListResponse(User $user, ActionCollection $actions, ?Task $task): JsonResponse
    {
        $includeActionTask = $task === null;
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        return $this->jsonResponseBuilder->build([
            'actions' => $this->actionResponseBuilder->buildActionListResponse($actions, $includeActionTask),
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
