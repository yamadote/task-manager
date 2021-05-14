<?php

namespace App\Controller;

use App\Config\HeaderLinkConfig;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/task")
 */
class TaskApiController extends AbstractController
{
    /** @var TaskRepository */
    private $taskRepository;

    public function __construct(
        TaskRepository $taskRepository,
        HeaderLinkConfig $headerLinkConfig
    ) {
        parent::__construct($headerLinkConfig);
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/", name="app_task_api_index", methods={"GET"})
     */
    public function showTasks(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserTasks($this->getUser());
        $root = null;
        foreach ($tasks as $task) {
            if (null === $task->getParent()) {
                $root = $task;
                break;
            }
        }
        $data = [];
        foreach ($tasks as $task) {
            if (null === $task->getParent()) {
                continue;
            }
            $parentId = $task->getParent()->getId();
            $data[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'parent' => $parentId === $root->getId() ? null : $parentId
            ];
        }
        return new JsonResponse($data);
    }
}
