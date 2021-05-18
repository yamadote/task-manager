<?php

namespace App\Response\Builder;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilder
{
    /**
     * @param Task[] $tasks
     * @return JsonResponse
     */
    public function buildListResponse(iterable $tasks): JsonResponse
    {
        $response = [];
        foreach ($tasks as $task) {
            $response[] = $this->buildTaskArrayResponse($task);
        }
        return new JsonResponse($response);
    }

    /**
     * @param Task $task
     * @return JsonResponse
     */
    public function buildTaskResponse(Task $task): JsonResponse
    {
        return new JsonResponse($this->buildTaskArrayResponse($task));
    }

    /**
     * @param Task $task
     * @return array
     */
    private function buildTaskArrayResponse(Task $task): array
    {
        $parent = $task->getParent();
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'parent' => $parent ? $parent->getId() : null,
            'link' => $task->getLink()
        ];
    }
}
