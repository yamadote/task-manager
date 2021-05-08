<?php

namespace App\Controller;

use App\Config\UserStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /** @var UserStatusConfig */
    private $userStatusConfig;

    public function __construct(UserStatusConfig $userStatusConfig)
    {
        $this->userStatusConfig = $userStatusConfig;
    }

    /**
     * @return User
     */
    protected function getUser(): UserInterface
    {
        return parent::getUser();
    }

    /**
     * @Route("", name="app_task_index", methods={"GET"})
     */
    public function index(TaskRepository $taskRepository, Request $request): Response
    {
        $statusSlug = $request->query->get('status');
        if (empty($statusSlug)) {
            $tasks = $taskRepository->findUserTasks($this->getUser());
            return $this->renderTaskListPage($tasks);
        }
        $statusId = $this->userStatusConfig->getStatusIdBySlug($statusSlug);
        $tasks = $taskRepository->findUserTasksByStatus($this->getUser(), $statusId);
        return $this->renderTaskListPage($tasks);
    }

    /**
     * @Route("/reminders", name="app_task_reminders", methods={"GET"})
     */
    public function reminders(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findUserReminders($this->getUser());
        return $this->renderTaskListPage($tasks);
    }

    /**
     * @Route("/todo", name="app_task_todo", methods={"GET"})
     */
    public function todo(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findUserTodoTasks($this->getUser());
        return $this->renderTaskListPage($tasks);
    }

    /**
     * @param Task[] $tasks
     * @return Response
     */
    private function renderTaskListPage(array $tasks): Response
    {
        $statusList = $this->userStatusConfig->getStatusList();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'statusList' => $statusList
        ]);
    }

    /**
     * @Route("/new", name="app_task_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $task->setUser($this->getUser());
        $currentTime = new DateTime();
        $task->setCreatedAt($currentTime);
        $task->setUpdatedAt($currentTime);
        $form = $this->createForm(TaskFormType::class, $task, [
            TaskFormType::NO_REMOVED_STATUS_OPTION => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        // todo: check if can edit

        $currentTime = new DateTime();
        $task->setUpdatedAt($currentTime);
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
}
