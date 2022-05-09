<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Builder\UserTaskSettingsBuilder;
use App\Composer\TaskResponseComposer;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Exception;
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

    private TaskRepository $taskRepository;
    private TaskResponseComposer $taskResponseComposer;
    private TaskStatusConfig $taskStatusConfig;
    private UserTaskSettingsBuilder $userTaskSettingsBuilder;
    private JsonResponseBuilder $jsonResponseBuilder;
    private TaskService $taskService;

    public function __construct(
        TaskRepository $taskRepository,
        TaskResponseComposer $taskResponseComposer,
        TaskStatusConfig $taskStatusConfig,
        UserTaskSettingsBuilder $userTaskSettingsBuilder,
        JsonResponseBuilder $jsonResponseBuilder,
        TaskService $taskService
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskResponseComposer = $taskResponseComposer;
        $this->taskStatusConfig = $taskStatusConfig;
        $this->userTaskSettingsBuilder = $userTaskSettingsBuilder;
        $this->jsonResponseBuilder = $jsonResponseBuilder;
        $this->taskService = $taskService;
    }

    /**
     * @Route("", name="app_api_task_all", methods={"GET"})
     */
    public function all(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserTasks($this->getUser());
        return $this->taskResponseComposer->composeListResponse($this->getUser(), $tasks);
    }

    /**
     * @Route("/reminders", name="app_api_task_reminders", methods={"GET"})
     */
    public function reminders(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        return $this->taskResponseComposer->composeListResponse($this->getUser(), $tasks);
    }

    /**
     * @Route("/todo", name="app_api_task_todo", methods={"GET"})
     */
    public function todo(): JsonResponse
    {
        $statusCollection = $this->taskStatusConfig->getTodoStatusCollection();
        $tasks = $this->taskRepository->findUserTasksByStatusList($this->getUser(), $statusCollection, true);
        return $this->taskResponseComposer->composeListResponse($this->getUser(), $tasks);
    }

    /**
     * @Route("/status/{status}", name="app_api_task_status", methods={"GET"})
     */
    public function status(Request $request): JsonResponse
    {
        $statusSlug = $request->attributes->get(self::STATUS_REQUEST_FIELD);
        if (!$this->taskStatusConfig->isStatusSlugExisting($statusSlug)) {
            return $this->jsonResponseBuilder->buildError('Task status not valid');
        }
        $tasks = $this->taskService->getTasksByStatus($this->getUser(), $statusSlug);
        return $this->taskResponseComposer->composeListResponse($this->getUser(), $tasks);
    }

    /**
     * @Route("/new", name="app_api_task_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $parent = $this->getParentFromRequest($request);
        if (null === $parent) {
            return $this->jsonResponseBuilder->buildError('Parent task not found');
        }
        $user = $this->getUser();
        if (!$parent->getUser()->equals($user)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $task = $this->taskService->createTask($user, $parent);
        $settings = $this->userTaskSettingsBuilder->buildDefaultSettings($task);
        return $this->taskResponseComposer->composeTaskResponse($user, $task, $settings);
    }

    private function getParentFromRequest(Request $request): ?Task
    {
        if (empty($request->request->get('parent'))) {
            return $this->taskRepository->findUserRootTask($this->getUser());
        }
        return $this->taskRepository->findOneBy(['id' => $request->request->get('parent')]);
    }

    /**
     * @Route("/{id}/edit", name="app_api_task_edit", methods={"POST"})
     * @throws Exception
     */
    public function edit(Task $task, Request $request): JsonResponse
    {
        if (!$this->canEditTask($task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $this->taskService->editTask($task, $request->request);
        return $this->jsonResponseBuilder->build();
    }

    /**
     * @Route("/{id}/edit/settings", name="app_api_task_edit_settings", methods={"POST"})
     * @throws Exception
     */
    public function editSettings(Task $task, Request $request): JsonResponse
    {
        if (!$this->canEditTask($task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $this->taskService->editTaskSettings($this->getUser(), $task, $request->request);
        return $this->jsonResponseBuilder->build();
    }

    /**
     * @Route("/{id}/delete", name="app_api_task_delete", methods={"POST"})
     */
    public function delete(Task $task): JsonResponse
    {
        if (!$this->canEditTask($task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
//        todo: stop period of task, maybe remove it also?
//        todo: investigate adding csrf token validation
//        $this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))
        $this->taskService->deleteTask($task);
        return $this->jsonResponseBuilder->build();
    }

    private function canEditTask(Task $task): bool
    {
        return $this->getUser()->equals($task->getUser()) && null !== $task->getParent();
    }
}
