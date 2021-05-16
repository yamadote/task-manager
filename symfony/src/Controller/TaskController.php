<?php

namespace App\Controller;

use App\Builder\TaskBuilder;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
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

    /** @var TaskBuilder */
    private $taskBuilder;

    public function __construct(
        TaskRepository $taskRepository,
        TaskResponseBuilder $taskResponseBuilder,
        TaskStatusConfig $taskStatusConfig,
        TaskBuilder $taskBuilder
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskResponseBuilder = $taskResponseBuilder;
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskBuilder = $taskBuilder;
    }

    /**
     * @Route("", name="app_api_task_all", methods={"GET"})
     */
    public function all(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserTasks($this->getUser());
        return $this->taskResponseBuilder->buildListResponse($tasks);
    }

    /**
     * @Route("/reminders", name="app_api_task_reminders", methods={"GET"})
     */
    public function reminders(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        return $this->taskResponseBuilder->buildListResponse($tasks);
    }

    /**
     * @Route("/todo", name="app_api_task_todo", methods={"GET"})
     */
    public function todo(): JsonResponse
    {
        $statusList = $this->taskStatusConfig->getTodoStatusIds();
        $tasks = $this->taskRepository->findUserTasksHierarchyByStatusList($this->getUser(), $statusList);
        return $this->taskResponseBuilder->buildListResponse($tasks);
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
        return $this->taskResponseBuilder->buildListResponse($tasks);
    }

    /**
     * @Route("/new", name="app_api_task_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        // todo: implement status setting dependeds on page
        if ($request->request->has('parent')) {
            $parent = $this->taskRepository->findOneBy(['id' => $request->request->get('parent')]);
            if (null === $parent) {
                return new JsonResponse(['error' => 'Parent task not found.'], 400);
            }
            if (!$this->getUser()->equals($parent->getUser())) {
                return new JsonResponse(['error' => 'Permission denied.'], 403);
            }
        } else {
            // todo: validate user id
            $parent = $this->taskRepository->findUserRootTask($this->getUser());
        }
        $task = $this->taskBuilder->buildFromRequest($request, $this->getUser(), $parent);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();
        return $this->taskResponseBuilder->buildTaskResponse($task);
    }

    /**
     * @Route("/{id}/delete", name="app_api_task_delete", methods={"POST"})
     */
    public function delete(Task $task): JsonResponse
    {
        if (!$this->canEditTask($task)) {
            return new JsonResponse(['error' => 'Permission denied'], 403);
        }
//          todo: stop period of task, maybe remove it also?
//        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
        $entityManager = $this->getDoctrine()->getManager();
        $children = $this->taskRepository->findChildren($task);
        $entityManager->remove($task);
        foreach ($children as $child) {
            $entityManager->remove($child);
        }
        $entityManager->flush();
        return new JsonResponse();
    }

    /**
     * @param Task $task
     * @return bool
     */
    private function canEditTask(Task $task): bool
    {
        return $this->getUser()->equals($task->getUser()) && null !== $task->getParent();
    }
}
