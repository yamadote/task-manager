<?php

namespace App\Controller;

use App\Builder\TaskBuilder;
use App\Builder\UserTaskSettingsBuilder;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Builder\TaskResponseBuilder;
use App\Repository\TrackedPeriodRepository;
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

    /** @var UserTaskSettingsRepository */
    private $userTaskSettingsRepository;

    /** @var UserTaskSettingsBuilder */
    private $userTaskSettingsBuilder;

    /** @var TrackedPeriodRepository */
    private $trackedPeriodRepository;

    public function __construct(
        TaskRepository $taskRepository,
        TaskResponseBuilder $taskResponseBuilder,
        TaskStatusConfig $taskStatusConfig,
        TaskBuilder $taskBuilder,
        UserTaskSettingsRepository $userTaskSettingsRepository,
        UserTaskSettingsBuilder $userTaskSettingsBuilder,
        TrackedPeriodRepository $trackedPeriodRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskResponseBuilder = $taskResponseBuilder;
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskBuilder = $taskBuilder;
        $this->userTaskSettingsRepository = $userTaskSettingsRepository;
        $this->userTaskSettingsBuilder = $userTaskSettingsBuilder;
        $this->trackedPeriodRepository = $trackedPeriodRepository;
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
        $isProgressStatus = $status->getId() === TaskStatusConfig::IN_PROGRESS_STATUS_ID;
        $fullHierarchy = !$isProgressStatus;

        $tasks = $this->taskRepository->findUserTasksByStatus($this->getUser(), $status->getId(), $fullHierarchy);
        $root = $this->findRootTask($tasks);

        if ($isProgressStatus) {
            $tasks = $this->addActiveTask($tasks);
        }

        return $this->taskResponseBuilder->buildListResponse($this->getUser(), $tasks, $root);
    }

    /**
     * @param Task[] $tasks
     * @return Task[]
     */
    private function addActiveTask(array $tasks): array
    {
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($this->getUser());
        if (null === $activePeriod) {
            return $tasks;
        }
        $hasTask = false;
        $activeTask = $activePeriod->getTask();
        foreach ($tasks as $task) {
            if ($task->equals($activeTask)) {
                $hasTask = true;
                break;
            }
        }
        if (!$hasTask) {
            $tasks[] = $activeTask;
        }
        return $tasks;
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
        $user = $this->getUser();
        if (!$parent->getUser()->equals($user)) {
            return $this->getPermissionDeniedResponse();
        }
        $root = $this->findRootTask([$parent]);
        $task = $this->taskBuilder->buildFromRequest($request, $user, $parent);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);

        $parentSettings = $this->userTaskSettingsRepository->findByUserAndTask($user, $parent);
        $parentSettings->setIsChildrenOpen(true);
        $entityManager->persist($parentSettings);

        $entityManager->flush();
        $settings = $this->userTaskSettingsBuilder->buildDefaultSettings($user, $task);
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
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse();
    }

    /**
     * @Route("/{id}/edit/settings", name="app_api_task_edit_settings", methods={"POST"})
     * @throws Exception
     * todo: refactor
     */
    public function editSettings(Task $task, Request $request): JsonResponse {
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
        return new JsonResponse();
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
