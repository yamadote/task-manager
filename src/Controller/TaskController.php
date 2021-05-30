<?php

namespace App\Controller;

use App\Builder\TaskBuilder;
use App\Builder\UserTaskSettingsBuilder;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Builder\TaskResponseBuilder;
use App\Repository\UserTaskSettingsRepository;
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
        return $this->taskResponseBuilder->buildListResponse($this->getUser(), $tasks, $root);
    }

    /**
     * @Route("/reminders", name="app_api_task_reminders", methods={"GET"})
     */
    public function reminders(): JsonResponse
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        $root = $this->findRootTask($tasks);
        return $this->taskResponseBuilder->buildListResponse($this->getUser(), $tasks, $root);
    }

    /**
     * @Route("/todo", name="app_api_task_todo", methods={"GET"})
     */
    public function todo(): JsonResponse
    {
        $statusList = $this->taskStatusConfig->getTodoStatusIds();
        $tasks = $this->taskRepository->findUserTasksByStatusList($this->getUser(), $statusList, true);
        $root = $this->findRootTask($tasks);
        return $this->taskResponseBuilder->buildListResponse($this->getUser(), $tasks, $root);
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
        return $this->taskResponseBuilder->buildListResponse($this->getUser(), $tasks, $root);
    }

    /**
     * @Route("/new", name="app_api_task_new", methods={"POST"})
     */
    public function new(Request $request, UserTaskSettingsBuilder $settingsBuilder): JsonResponse
    {
        $parent = $this->getParentFromRequest($request);
        if (null === $parent) {
            return new JsonResponse(['error' => 'Parent task not found.'], 400);
        }
        if (!$parent->getUser()->equals($this->getUser())) {
            return $this->getPermissionDeniedResponse();
        }
        $root = $this->findRootTask([$parent]);
        $task = $this->taskBuilder->buildFromRequest($request, $this->getUser(), $parent);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();
        $settings = $settingsBuilder->buildDefaultSettings($this->getUser(), $task);
        return $this->taskResponseBuilder->buildTaskResponse($task, $settings, $root);
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
        if (!$task->getUser()->equals($this->getUser())) {
            return $this->getPermissionDeniedResponse();
        }
        $entityManager = $this->getDoctrine()->getManager();
        $changed = [];
        if ($request->request->has('title')) {
            $task->setTitle($request->request->get('title'));
            $changed[] = 'title';
        }
        if ($request->request->has('link')) {
            $task->setLink($request->request->get('link'));
            $changed[] = 'link';
        }
        if ($request->request->has('reminder')) {
            $reminder = $request->request->get('reminder');
            if (null === $reminder) {
                $task->setReminder(null);
            } else {
                $date = new DateTime();
                $date->setTimestamp($request->request->get('reminder'));
                $task->setReminder($date);
            }
            $changed[] = 'reminder';
        }
        if ($request->request->has('status')) {
            $task->setStatus($request->request->get('status'));
            $changed[] = 'status';
        }
        $entityManager->flush();
        return new JsonResponse(['changed' => $changed]);
    }

    /**
     * @Route("/{id}/edit/settings", name="app_api_task_edit", methods={"POST"})
     * @throws Exception
     * todo: refactor
     */
    public function editSettings(
        Task $task,
        Request $request,
        UserTaskSettingsRepository $settingsRepository
    ): JsonResponse {
        $changed = [];
        $setting = $settingsRepository->findByUserAndTask($this->getUser(), $task);
        if ($request->request->has('isChildrenOpen')) {
            $setting->setIsChildrenOpen($request->request->get('isChildrenOpen'));
            $changed[] = 'isChildrenOpen';
        }
        if ($request->request->has('isAdditionalPanelOpen')) {
            $setting->setIsAdditionalPanelOpen($request->request->get('isAdditionalPanelOpen'));
            $changed[] = 'isAdditionalPanelOpen';
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($setting);
        $manager->flush();
        return new JsonResponse(['changed' => $changed]);
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
