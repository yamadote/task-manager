<?php

namespace App\Builder;

use App\Collection\ActionCollection;

class ActionResponseBuilder
{
    public function buildActionListResponse(ActionCollection $actions): array
    {
        $response = [];
        foreach ($actions as $action) {
            $response[] = [
                'id' => $action->getId(),
                'task' => [
                    'id' => $action->getTask()->getId(),
                    'title' => $action->getTask()->getTitle(),
                ],
                'type' => $action->getType(),
                'message' => $action->getMessage(),
                'createdAt' => $action->getCreatedAt()->getTimestamp()
            ];
        }
        return $response;
    }
}