<?php

namespace App\Composer;

use App\Builder\ActionResponseBuilder;
use App\Builder\JsonResponseBuilder;
use App\Collection\ActionCollection;
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

    public function composeListResponse(User $user, ActionCollection $actions): JsonResponse
    {
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        return $this->jsonResponseBuilder->build([
            'actions' => $this->actionResponseBuilder->buildActionListResponse($actions),
            'reminderNumber' => $reminderNumber
        ]);
    }
}
