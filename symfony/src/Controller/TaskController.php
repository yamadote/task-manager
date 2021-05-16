<?php

namespace App\Controller;

use App\Config\TaskStatusConfig;
use App\Repository\TaskRepository;
use App\Response\Builder\TaskResponseBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/api/tasks")
 */
class TaskController extends AbstractController
{
    private const STATUS_REQUEST_FIELD = 'status';

    /** @var TaskRepository */
    private $taskRepository;

    /** @var TaskResponseBuilder */
    private $taskResponseBuilder;

    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    public function __construct(
        TaskRepository $taskRepository,
        TaskResponseBuilder $taskResponseBuilder,
        TaskStatusConfig $taskStatusConfig
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskResponseBuilder = $taskResponseBuilder;
        $this->taskStatusConfig = $taskStatusConfig;
    }

    /**
     * @Route("", name="app_api_task_all", methods={"GET"})
     */
    public function all(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserTasks($this->getUser());
        return $this->taskResponseBuilder->build($tasks);
    }

    /**
     * @Route("/reminders", name="app_api_task_reminders", methods={"GET"})
     */
    public function reminders(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        return $this->taskResponseBuilder->build($tasks);
    }

    /**
     * @Route("/todo", name="app_api_task_todo", methods={"GET"})
     */
    public function todo(): JsonResponse
    {
        $statusList = $this->taskStatusConfig->getTodoStatusIds();
        $tasks = $this->taskRepository->findUserTasksHierarchyByStatusList($this->getUser(), $statusList);
        return $this->taskResponseBuilder->build($tasks);
    }

    /**
     * @Route("/status/{status}", name="app_api_task_status", methods={"GET"})
     */
    public function status(Request $request): JsonResponse
    {
        $statusSlug = $request->attributes->get(self::STATUS_REQUEST_FIELD);
        if (!$this->taskStatusConfig->isStatusSlugExisting($statusSlug)) {
            return new JsonResponse(null, 400);
        }
        $status = $this->taskStatusConfig->getStatusBySlug($statusSlug);
        $tasks = $this->taskRepository->findUserTasksHierarchyByStatus($this->getUser(), $status->getId());
        return $this->taskResponseBuilder->build($tasks);
    }
}
