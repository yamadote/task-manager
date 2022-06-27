<?php

namespace App\Builder;

use App\Collection\HistoryActionCollection;
use App\Entity\HistoryAction;
use App\Entity\Task;

class HistoryActionResponseBuilder
{
    public function buildActionListResponse(HistoryActionCollection $actions, bool $includeActionTask): array
    {
        $response = [];
        foreach ($actions as $action) {
            $response[] = $this->buildActionResponse($action, $includeActionTask);
        }
        return $response;
    }

    private function buildActionResponse(HistoryAction $action, bool $includeActionTask): array
    {
        $response = [
            'id' => $action->getId(),
            'type' => $action->getType(),
            'message' => $action->getMessage(),
            'createdAt' => $action->getCreatedAt()->getTimestamp()
        ];
        if ($includeActionTask) {
            $response['task'] = $this->buildTaskResponse($action->getTask());
        }
        return $response;
    }

    private function buildTaskResponse(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
        ];
    }
}