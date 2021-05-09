<?php

namespace App\Controller;

use App\Builder\TaskBuilder;
use App\Config\HeaderLinkConfig;
use App\Config\TaskStatusConfig;
use App\Entity\HeaderLink;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 * @method User getUser
 */
class TaskController extends AbstractController
{
    private const PARENT_REQUEST_FIELD = 'parent';
    private const STATUS_REQUEST_FIELD = 'status';
    private const LINK_REQUEST_FIELD = 'link';
    public const INDEX_ROUTE = 'app_task_index';

    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    /** @var TaskRepository */
    private $taskRepository;

    /** @var HeaderLinkConfig */
    private $headerLinkConfig;

    /** @var TaskBuilder */
    private $taskBuilder;

    public function __construct(
        TaskStatusConfig $taskStatusConfig,
        TaskRepository $taskRepository,
        HeaderLinkConfig $headerLinkConfig,
        TaskBuilder $taskBuilder
    ) {
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskRepository = $taskRepository;
        $this->headerLinkConfig = $headerLinkConfig;
        $this->taskBuilder = $taskBuilder;
    }

    /**
     * @Route("/", name="app_task_index", methods={"GET"})
     * @Route("/{parent}", name="app_task_index_parent", methods={"GET"}, requirements={"parent"="\d+"})
     */
    public function index(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $tasks = $this->taskRepository->findUserTasks($this->getUser(), $parent);
        $root = $this->headerLinkConfig->getAllTasksLink();
        return $this->renderTaskListPage($tasks, $parent, $root);
    }

    /**
     * @Route("/status/{status}", name="app_task_status", methods={"GET"})
     * @Route("/{parent}/status/{status}", name="app_task_status_parent",
     *     methods={"GET"}, requirements={"parent"="\d+"})
     */
    public function statusTab(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $statusSlug = $request->attributes->get(self::STATUS_REQUEST_FIELD);
        if (!$this->taskStatusConfig->isStatusSlugExisting($statusSlug)) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }
        $status = $this->taskStatusConfig->getStatusBySlug($statusSlug);
        $tasks = $this->taskRepository->findUserTasksByStatus($this->getUser(), $status->getId(), $parent);
        $root = $this->headerLinkConfig->getLinkByTaskStatus($status);
        return $this->renderTaskListPage($tasks, $parent, $root, ['status' => $status]);
    }

    /**
     * @Route("/reminders", name="app_task_reminders", methods={"GET"})
     */
    public function remindersTab(): Response
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        $root = $this->headerLinkConfig->getRemindersLink();
        return $this->renderTaskListPage($tasks, null, $root);
    }

    /**
     * @Route("/todo", name="app_task_todo", methods={"GET"})
     * @Route("/{parent}/todo", name="app_task_todo_parent", methods={"GET"}, requirements={"parent"="\d+"})
     */
    public function todoTab(Request $request): Response
    {
        $parent = $this->getParentFromRequest($request);
        $tasks = $this->taskRepository->findUserTodoTasks($this->getUser(), $parent);
        $root = $this->headerLinkConfig->getTodoLink();
        return $this->renderTaskListPage($tasks, $parent, $root);
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
            'root' => $link
        ]);
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task): Response
    {
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
        return $this->getUser()->equals($task->getUser());
    }

    /**
     * @param Task[] $tasks
     * @param string $category
     * @param Task|null $parent
     * @param HeaderLink $root
     * @param array $additional
     * @return Response
     */
    private function renderTaskListPage(
        array $tasks,
        ?Task $parent,
        HeaderLink $root,
        array $additional = []
    ): Response {
        $path = $this->taskRepository->getPath($parent);
        $statusList = $this->taskStatusConfig->getStatusList();
        return $this->render('task/index.html.twig', array_merge([
            'tasks' => $tasks,
            'statusList' => $statusList,
            'parent' => $parent,
            'path' => $path,
            'root' => $root
        ], $additional));
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function redirectBack(Request $request): Response
    {
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @param HeaderLink $headerLink
     * @param Task|null $task
     * @return Response
     */
    private function redirectToHeaderLink(HeaderLink $link, ?Task $task): Response
    {
        if ($task && $task->getParent()) {
            return $this->redirectToRoute($link->getParentRoute(), $link->getParentRouteParams($task->getParent()));
        }
        return $this->redirectToRoute($link->getRoute(), $link->getRouteParams());
    }

    /**
     * @param Request $request
     * @return HeaderLink
     */
    private function getHeaderLinkFromRequest(Request $request): HeaderLink
    {
        $linkId = $request->get(self::LINK_REQUEST_FIELD);
        if (!$this->headerLinkConfig->isHeaderLinkIdExists($linkId)) {
            $linkId = $this->headerLinkConfig->getAllTasksLink();
        }
        return $this->headerLinkConfig->getLinkById($linkId);
    }

    /**
     * @param Request $request
     * @return Task
     */
    private function getParentFromRequest(Request $request): ?Task
    {
        $parentId = $request->attributes->get(self::PARENT_REQUEST_FIELD);
        if (null === $parentId) {
            return null;
        }
        $parent = $this->taskRepository->find($parentId);
        if (null === $parent) {
            return null;
        }
        if (!$this->getUser()->equals($parent->getUser())) {
            $parent = null;
        }
        return $parent;
    }
}
