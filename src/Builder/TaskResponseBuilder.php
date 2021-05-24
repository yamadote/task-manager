<?php

namespace App\Builder;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilder
{
    /**
     * @param iterable $tasks
     * @param Task $root
     * @return JsonResponse
     */
    public function buildListResponse(iterable $tasks, Task $root): JsonResponse
    {
        $response = [];
        /** @var Task $task */
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                continue;
            }
            $response[] = $this->buildTaskArrayResponse($task, $root);
        }
        return new JsonResponse($response);
    }

    /**
     * @param Task $task
     * @param Task $root
     * @return JsonResponse
     */
    public function buildTaskResponse(Task $task, Task $root): JsonResponse
    {
        return new JsonResponse($this->buildTaskArrayResponse($task, $root));
    }

    /**
     * @param Task $task
     * @param Task $root
     * @return array
     */
    private function buildTaskArrayResponse(Task $task, Task $root): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'parent' => $this->getParentId($task, $root),
            'link' => $task->getLink()
        ];
    }

    /**
     * @param Task $task
     * @param Task $root
     * @return int|null
     */
    private function getParentId(Task $task, Task $root): ?int
    {
        if (null === $task->getParent()) {
            return null;
        }
        if ($task->getParent()->equals($root)) {
            return null;
        }
        return $task->getParent()->getId();
    }
}
