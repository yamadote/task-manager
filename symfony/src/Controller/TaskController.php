<?php

namespace App\Controller;

use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    /** @var TaskRepository */
    private $taskRepository;

    public function __construct(TaskStatusConfig $taskStatusConfig, TaskRepository $taskRepository)
    {
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @return User
     */
    protected function getUser(): UserInterface
    {
        return parent::getUser();
    }

    /**
     * @Route("/status/{status}", name="app_task_status", methods={"GET"})
     * @Route("/{parent}/status/{status}", name="app_task_status_parent", methods={"GET"})
     */
    public function status(Request $request, ?Task $parent): Response
    {
        // todo: check if slug is valid
        $status = $this->taskStatusConfig->getStatusBySlug($request->attributes->get('status'));
        $tasks = $this->taskRepository->findUserTasksByStatus($this->getUser(), $status->getId(), $parent);
        return $this->renderTaskListPage($tasks, $status->getTitle(), $parent);
    }

    /**
     * @Route("/reminders", name="app_task_reminders", methods={"GET"})
     */
    public function reminders(): Response
    {
        $tasks = $this->taskRepository->findUserReminders($this->getUser());
        return $this->renderTaskListPage($tasks, 'Reminders', null);
    }

    /**
     * @Route("/todo", name="app_task_todo", methods={"GET"})
     * @Route("/{parent}/todo", name="app_task_todo_parent", methods={"GET"})
     */
    public function todo(?Task $parent): Response
    {
        $tasks = $this->taskRepository->findUserTodoTasks($this->getUser(), $parent);
        return $this->renderTaskListPage($tasks, 'Todo', $parent);
    }

    /**
     * @Route("/new", name="app_task_new", methods={"GET","POST"})
     * @Route("/{parent}/new", name="app_task_new_parent", methods={"GET","POST"})
     */
    public function new(Request $request, ?Task $parent): Response
    {
        $task = new Task();
        $task->setUser($this->getUser());
        if ($parent) {
            if ($parent->getUser()->getId() !== $this->getUser()->getId()) {
                return $this->redirectToRoute('app_task_new');
            }
            $task->setParent($parent);
        }
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($task->getParent() && $task->getParent()->getUser()->getId() !== $this->getUser()->getId()) {
            $form->get('parent')->addError(new FormError("Your can't use someone else's task!"));
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            if ($task->getParent()) {
                return $this->redirectToRoute("app_task_index_parent", [
                    'parent' => $task->getParent()->getId()
                ]);
            }
            return $this->redirectToRoute('app_task_index');
        }
        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'parent' => $task->getParent(),
            'path' => $this->taskRepository->getPath($task->getParent())
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        if ($task->getUser()->getId() !== $this->getUser()->getId()) {
            // todo: show error
            return $this->redirectToRoute('app_task_index');
        }
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_task_index');
        }
        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'parent' => $task->getParent(),
            'path' => $this->taskRepository->getPath($task)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task): Response
    {
        if ($task->getUser()->getId() !== $this->getUser()->getId()) {
            // todo: show error
            return $this->redirectToRoute('app_task_index');
        }
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $children = $this->taskRepository->getChildren($task);
            $entityManager->remove($task);
            foreach ($children as $child) {
                $entityManager->remove($child);
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index');
    }

    /**
     * @Route("/", name="app_task_index", methods={"GET"})
     * @Route("/{parent}", name="app_task_index_parent", methods={"GET"})
     */
    public function index(?Task $parent): Response
    {
        $tasks = $this->taskRepository->findUserTasks($this->getUser(), $parent);
        return $this->renderTaskListPage($tasks, "All", $parent);
    }

    /**
     * @param Task[] $tasks
     * @param string $category
     * @param Task|null $parent
     * @return Response
     */
    private function renderTaskListPage(array $tasks, string $category, ?Task $parent): Response
    {
        $path = $this->taskRepository->getPath($parent);
        $statusList = $this->taskStatusConfig->getStatusList();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'statusList' => $statusList,
            'category' => $category,
            'parent' => $parent,
            'path' => $path
        ]);
    }
}
