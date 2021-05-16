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
    public function build(iterable $tasks): JsonResponse
    {
        $response = [];
        foreach ($tasks as $task) {
            $parent = $task->getParent();
            $response[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'parent' => $parent ? $parent->getId() : null,
                'link' => $task->getLink()
            ];
        }
        return new JsonResponse($response);
    }
}
