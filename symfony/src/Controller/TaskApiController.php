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
        $root = $this->taskRepository->findUserRootTask($this->getUser());
        $tasks = $this->taskRepository->findUserTasks($this->getUser(), $root);
        $data = [];
        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle()
            ];
        }
        return new JsonResponse($data);
    }
}
