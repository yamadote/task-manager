<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Builder\UserTaskSettingsBuilder;
use App\Composer\TaskResponseComposer;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserTaskSettingsRepository;
use App\Service\TaskService;
use DateTime;
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
    private UserTaskSettingsRepository $userTaskSettingsRepository;
    private UserTaskSettingsBuilder $userTaskSettingsBuilder;
    private JsonResponseBuilder $jsonResponseBuilder;
    private TaskService $taskService;

    /**
     * @param TaskRepository $taskRepository
     * @param TaskResponseComposer $taskResponseComposer
     * @param TaskStatusConfig $taskStatusConfig
     * @param UserTaskSettingsRepository $userTaskSettingsRepository
     * @param UserTaskSettingsBuilder $userTaskSettingsBuilder
     * @param JsonResponseBuilder $jsonResponseBuilder
     * @param TaskService $taskService
     */
    public function __construct(
        TaskRepository $taskRepository,
        TaskResponseComposer $taskResponseComposer,
        TaskStatusConfig $taskStatusConfig,
        UserTaskSettingsRepository $userTaskSettingsRepository,
        UserTaskSettingsBuilder $userTaskSettingsBuilder,
        JsonResponseBuilder $jsonResponseBuilder,
        TaskService $taskService
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskResponseComposer = $taskResponseComposer;
        $this->taskStatusConfig = $taskStatusConfig;
        $this->userTaskSettingsRepository = $userTaskSettingsRepository;
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
            return $this->jsonResponseBuilder->build(null, 400);
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
            return $this->jsonResponseBuilder->build(['error' => 'Parent task not found.'], 400);
        }
        $user = $this->getUser();
        if (!$parent->getUser()->equals($user)) {
            return $this->getPermissionDeniedResponse();
        }
        $task = $this->taskService->createTask($user, $parent);
        $settings = $this->userTaskSettingsBuilder->buildDefaultSettings($task);
        return $this->taskResponseComposer->composeTaskResponse($user, $task, $settings);
    }

    private function getParentFromRequest(Request $request): Task
    {
        if (empty($request->request->get('parent'))) {
            return $this->taskRepository->findUserRootTask($this->getUser());
        }
        return $this->taskRepository->findOneBy(['id' => $request->request->get('parent')]);
    }

    /**
     * @Route("/{id}/edit", name="app_api_task_edit", methods={"POST"})
     * @throws Exception
     * todo: refactor
     */
    public function edit(Task $task, Request $request): JsonResponse
    {
        if (!$this->canEditTask($task)) {
            return $this->getPermissionDeniedResponse();
        }
        if ($request->request->has('title')) {
            $task->setTitle($request->request->get('title'));
        }
        if ($request->request->has('link')) {
            $task->setLink($request->request->get('link'));
        }
        if ($request->request->has('reminder')) {
            $reminder = $request->request->get('reminder');
            $task->setReminder($reminder ? (new DateTime())->setTimestamp($reminder) : null);
        }
        if ($request->request->has('status')) {
            $task->setStatus($request->request->get('status'));
        }
        if ($request->request->has('description')) {
            $task->setDescription($request->request->get('description'));
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->jsonResponseBuilder->build();
    }

    /**
     * @Route("/{id}/edit/settings", name="app_api_task_edit_settings", methods={"POST"})
     * @throws Exception
     * todo: refactor
     */
    public function editSettings(Task $task, Request $request): JsonResponse
    {
        $setting = $this->userTaskSettingsRepository->findByUserAndTask($this->getUser(), $task);
        if ($request->request->has('isChildrenOpen')) {
            $setting->setIsChildrenOpen($request->request->get('isChildrenOpen'));
        }
        if ($request->request->has('isAdditionalPanelOpen')) {
            $setting->setIsAdditionalPanelOpen($request->request->get('isAdditionalPanelOpen'));
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($setting);
        $entityManager->flush();
        return $this->jsonResponseBuilder->build();
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
        $children = $this->taskRepository->findChildren($task);
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($children as $child) {
            $entityManager->remove($child);
        }
        $entityManager->remove($task);
        $entityManager->flush();
        return $this->jsonResponseBuilder->build();
    }

    private function canEditTask(Task $task): bool
    {
        return $this->getUser()->equals($task->getUser()) && null !== $task->getParent();
    }

    private function getPermissionDeniedResponse(): JsonResponse
    {
        return $this->jsonResponseBuilder->build(['error' => 'Permission denied'], 403);
    }
}
