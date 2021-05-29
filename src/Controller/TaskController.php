<?php

namespace App\Controller;

use App\Builder\TaskBuilder;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\TaskTitleEditLog;
use App\Repository\TaskRepository;
use App\Builder\TaskResponseBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/internal-api/tasks")
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
        $root = $this->findRootTask($tasks);
        return $this->taskResponseBuilder->buildListResponse($tasks, $root);
    }

    /**
     * @Route("/reminders", name="app_api_task_reminders", methods={"GET"})
     */
    public function reminders(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        $root = $this->findRootTask($tasks);
        return $this->taskResponseBuilder->buildListResponse($tasks, $root);
    }

    /**
     * @Route("/todo", name="app_api_task_todo", methods={"GET"})
     */
    public function todo(): JsonResponse
    {
        $statusList = $this->taskStatusConfig->getTodoStatusIds();
        $tasks = $this->taskRepository->findUserTasksByStatusList($this->getUser(), $statusList, true);
        $root = $this->findRootTask($tasks);
        return $this->taskResponseBuilder->buildListResponse($tasks, $root);
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
        $fullHierarchy = $status->getId() !== TaskStatusConfig::IN_PROGRESS_STATUS_ID;
        $tasks = $this->taskRepository->findUserTasksByStatus($this->getUser(), $status->getId(), $fullHierarchy);
        $root = $this->findRootTask($tasks);
        return $this->taskResponseBuilder->buildListResponse($tasks, $root);
    }

    /**
     * @Route("/new", name="app_api_task_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $parent = $this->getParentFromRequest($request);
        if (null === $parent) {
            return new JsonResponse(['error' => 'Parent task not found.'], 400);
        }
        if (!$parent->getUser()->equals($this->getUser())) {
            return $this->getPermissionDeniedResponse();
        }
        // todo: implement status setting depending on page
        $root = $this->findRootTask([$parent]);
        $task = $this->taskBuilder->buildFromRequest($request, $this->getUser(), $parent);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();
        return $this->taskResponseBuilder->buildTaskResponse($task, $root);
    }

    /**
     * @param Task[] $tasks
     * @return Task
     */
    private function findRootTask(array $tasks): Task
    {
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                return $task;
            }
        }
        return $this->taskRepository->findUserRootTask($this->getUser());
    }

    /**
     * @param Request $request
     * @return Task
     */
    private function getParentFromRequest(Request $request): Task
    {
        if (!$request->request->has('parent')) {
            return $this->taskRepository->findUserRootTask($this->getUser());
        }
        return $this->taskRepository->findOneBy(['id' => $request->request->get('parent')]);
    }

    /**
     * @Route("/{id}/edit", name="app_api_task_edit", methods={"POST"})
     */
    public function edit(Task $task, Request $request): JsonResponse
    {
        if (!$task->getUser()->equals($this->getUser())) {
            return $this->getPermissionDeniedResponse();
        }
        $entityManager = $this->getDoctrine()->getManager();
        // todo: refactor
        $changed = [];
        if ($this->applyTitleEdit($task, $request)) {
            $changed[] = 'title';
        }
        if ($this->applyLinkEdit($task, $request)) {
            $changed[] = 'link';
        }
        if ($this->applyStatusEdit($task, $request)) {
            $changed[] = 'status';
        }
        $entityManager->flush();
        return new JsonResponse(['changed' => $changed]);
    }

    /**
     * @param Task $task
     * @param Request $request
     * @return bool
     */
    private function applyTitleEdit(Task $task, Request $request): bool
    {
        if (!$request->request->has('title')) {
            return false;
        }
        $title = $request->request->get('title');
        if ($task->getTitle() === $title) {
            return false;
        }
        $task->setTitle($title);
        $taskTitleEditLog = new TaskTitleEditLog();
        $taskTitleEditLog->setTask($task);
        $taskTitleEditLog->setUser($this->getUser());
        $taskTitleEditLog->setTitle($title);
        $this->getDoctrine()->getManager()->persist($taskTitleEditLog);
        return true;
    }

    /**
     * @param Task $task
     * @param Request $request
     * @return bool
     */
    private function applyLinkEdit(Task $task, Request $request): bool
    {
        if (!$request->request->has('link')) {
            return false;
        }
        $link = $request->request->get('link');
        if ($task->getLink() === $link) {
            return false;
        }
        $task->setLink($link);
        return true;
    }

    /**
     * @param Task $task
     * @param Request $request
     * @return bool
     */
    private function applyStatusEdit(Task $task, Request $request): bool
    {
        if (!$request->request->has('status')) {
            return false;
        }
        $status = $request->request->get('status');
        if ($task->getStatus() === $status) {
            return false;
        }
        $task->setStatus($status);
        return true;
    }

    /**
     * @Route("/{id}/delete", name="app_api_task_delete", methods={"POST"})
     */
    public function delete(Task $task): JsonResponse
    {
        if (!$this->canEditTask($task)) {
            return $this->getPermissionDeniedResponse();
        }
//        todo: stop period of task, maybe remove it also?
//        todo: investigate adding csrf token validation
//        $this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))
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

    /**
     * @return JsonResponse
     */
    private function getPermissionDeniedResponse(): JsonResponse
    {
        return new JsonResponse(['error' => 'Permission denied'], 403);
    }
}
