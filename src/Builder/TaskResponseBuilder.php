<?php

namespace App\Builder;

use App\Config\TaskStatusConfig;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilder
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    /**
     * TaskResponseBuilder constructor.
     * @param TaskStatusConfig $taskStatusConfig
     */
    public function __construct(TaskStatusConfig  $taskStatusConfig)
    {
        $this->taskStatusConfig = $taskStatusConfig;
    }

    /**
     * @param iterable $tasks
     * @param Task $root
     * @return JsonResponse
     */
    public function buildListResponse(iterable $tasks, Task $root): JsonResponse
    {
        $tasksResponse = [];
        /** @var Task $task */
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                continue;
            }
            $tasksResponse[] = $this->buildTaskArrayResponse($task, $root);
        }
        $statusesResponse = [];
        foreach ($this->taskStatusConfig->getStatusList() as $status) {
            $statusesResponse[] = [
                'id' => $status->getId(),
                'title' => $status->getTitle(),
                'color' => $status->getColor()
            ];
        }
        return new JsonResponse([
            'statuses' => $statusesResponse,
            'tasks' => $tasksResponse
        ]);
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
        $reminder = $task->getReminder();
        $createdAt = $task->getCreatedAt();
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'parent' => $this->getParentId($task, $root),
            'link' => $task->getLink(),
            'reminder' => $reminder ? $reminder->getTimestamp() : null,
            'createdAt' => $createdAt ? $createdAt->getTimestamp() : null,
            'status' => $task->getStatus(),
            'isAdditionalPanelOpen' => false,
            'isChildrenOpen' => false
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
