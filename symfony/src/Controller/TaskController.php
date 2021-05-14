<?php

namespace App\Controller;

use App\Builder\TaskBuilder;
use App\Config\HeaderLinkConfig;
use App\Config\TaskStatusConfig;
use App\Entity\HeaderLink;
use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    private const PARENT_REQUEST_FIELD = 'parent';
    private const STATUS_REQUEST_FIELD = 'status';
    public const INDEX_ROUTE = 'app_task_index';

    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    /** @var TaskRepository */
    private $taskRepository;

    /** @var HeaderLinkConfig */
    private $headerLinkConfig;

    /** @var TaskBuilder */
    private $taskBuilder;

    /** @var TrackedPeriodRepository */
    private $trackedPeriodRepository;

    public function __construct(
        TaskStatusConfig $taskStatusConfig,
        TaskRepository $taskRepository,
        HeaderLinkConfig $headerLinkConfig,
        TaskBuilder $taskBuilder,
        TrackedPeriodRepository $trackedPeriodRepository
    ) {
        parent::__construct($headerLinkConfig);
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskRepository = $taskRepository;
        $this->headerLinkConfig = $headerLinkConfig;
        $this->taskBuilder = $taskBuilder;
        $this->trackedPeriodRepository = $trackedPeriodRepository;
    }

    /**
     * @Route("/", name="app_task_index", methods={"GET"})
     * @Route("/{parent}", name="app_task_index_parent", methods={"GET"}, requirements={"parent"="\d+"})
     */
    public function showTasks(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $tasks = $this->taskRepository->findUserTasksByParent($this->getUser(), $parent);
        $link = $this->headerLinkConfig->getAllTasksLink();
        return $this->renderTaskListPage($tasks, $parent, $link);
    }

    /**
     * @Route("/status/{status}", name="app_task_status", methods={"GET"})
     * @Route("/{parent}/status/{status}", name="app_task_status_parent",
     *     methods={"GET"}, requirements={"parent"="\d+"})
     */
    public function showTasksByStatus(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $statusSlug = $request->attributes->get(self::STATUS_REQUEST_FIELD);
        if (!$this->taskStatusConfig->isStatusSlugExisting($statusSlug)) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }
        $user = $this->getUser();
        $status = $this->taskStatusConfig->getStatusBySlug($statusSlug);
        $link = $this->headerLinkConfig->getLinkByTaskStatus($status);
        if ($link->hasParentLink()) {
            $tasks = $this->taskRepository->findUserTasksHierarchyByStatus($user, $parent, $status->getId());
        } else {
            $tasks = $this->taskRepository->findUserTasksByStatus($user, $status->getId());
        }
        return $this->renderTaskListPage($tasks, $parent, $link, ['newTaskStatus' => $status]);
    }

    /**
     * @Route("/reminders", name="app_task_reminders", methods={"GET"})
     */
    public function showReminderTasks(): Response
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        $link = $this->headerLinkConfig->getRemindersLink();
        return $this->renderTaskListPage($tasks, $this->getRootTask(), $link);
    }

    /**
     * @Route("/todo", name="app_task_todo", methods={"GET"})
     * @Route("/{parent}/todo", name="app_task_todo_parent", methods={"GET"}, requirements={"parent"="\d+"})
     */
    public function showTodoTasks(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $statusList = $this->taskStatusConfig->getTodoStatusIds();
        $tasks = $this->taskRepository->findUserTasksHierarchyByStatusList($this->getUser(), $parent, $statusList);
        $link = $this->headerLinkConfig->getTodoLink();
        return $this->renderTaskListPage($tasks, $parent, $link);
    }

    /**
     * @Route("/new", name="app_task_new", methods={"GET","POST"})
     * @Route("/{parent}/new", name="app_task_new_parent", methods={"GET","POST"}, requirements={"parent"="\d+"})
     */
    public function new(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $task = $this->taskBuilder->buildFromRequest($request, $this->getUser(), $parent);
        if (!$this->getUser()->equals($task->getUser())) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectBack($request);
    }

    /**
     * @Route("/{id}/edit", name="app_task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        if (!$this->canEditTask($task)) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        $link = $this->getHeaderLinkFromRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToHeaderLink($link, $task);
        }
        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'parent' => $task->getParent(),
            'path' => $this->taskRepository->getPath($task),
            'link' => $link
        ]);
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task): Response
    {
        // todo: stop period of task, maybe remove it also?
        if (!$this->canEditTask($task)) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $children = $this->taskRepository->findChildren($task);
            $entityManager->remove($task);
            foreach ($children as $child) {
                $entityManager->remove($child);
            }
            $entityManager->flush();
        }
        return $this->redirectBack($request);
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
     * @param Task[] $tasks
     * @param string $category
     * @param Task|null $parent
     * @param HeaderLink $link
     * @param array $additional
     * @return Response
     */
    private function renderTaskListPage(
        array $tasks,
        ?Task $parent,
        HeaderLink $link,
        array $additional = []
    ): Response {
        $path = $link->hasParentLink() ? $this->taskRepository->getPath($parent) : [];
        $statusList = $this->taskStatusConfig->getStatusList();
        return $this->render('task/index.html.twig', array_merge([
            'tasks' => $tasks,
            'statusList' => $statusList,
            'parent' => $parent,
            'path' => $path,
            'link' => $link,
            'active' => $this->trackedPeriodRepository->getActivePeriod($this->getUser())
        ], $additional));
    }

    /**
     * @param Request $request
     * @return Task
     */
    private function getParentFromRequest(Request $request): Task
    {
        $parentId = $request->attributes->get(self::PARENT_REQUEST_FIELD);
        if (null === $parentId) {
            return $this->getRootTask();
        }
        $parent = $this->taskRepository->find($parentId);
        if (null === $parent) {
            return $this->getRootTask();
        }
        if (!$this->getUser()->equals($parent->getUser())) {
            return $this->getRootTask();
        }
        return $parent;
    }

    /**
     * @return Task
     */
    private function getRootTask(): Task
    {
        return $this->taskRepository->findUserRootTask($this->getUser());
    }
}
