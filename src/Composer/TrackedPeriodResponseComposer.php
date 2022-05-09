<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TrackedPeriodResponseComposer
{
    private TaskRepository $taskRepository;
    private JsonResponseBuilder $jsonResponseBuilder;

    public function __construct(TaskRepository $taskRepository, JsonResponseBuilder $jsonResponseBuilder)
    {
        $this->taskRepository = $taskRepository;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
    }

    public function compose(Task $task): JsonResponse
    {
        $path = $this->taskRepository->getTaskPath($task);
        return $this->jsonResponseBuilder->build(['activeTask' => [
            'path' => $path->getIds()
        ]]);
    }
}