<?php

namespace App\Controller;

use App\Config\TaskStatusConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private TaskStatusConfig $taskStatusConfig;

    public function __construct(TaskStatusConfig $taskStatusConfig)
    {
        $this->taskStatusConfig = $taskStatusConfig;
    }

    /**
     * @Route("", name="app_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_task_index');
    }

    /**
     * @Route("/tasks", name="app_task_index", methods={"GET"})
     * @Route("/tasks/todo", name="app_task_todo", methods={"GET"})
     * @Route("/tasks/reminders", name="app_task_reminders", methods={"GET"})
     * @Route("/{root}/tasks", name="app_parent_task_index", methods={"GET"}, requirements={"root"="\d+"})
     * @Route("/{root}/tasks/todo", name="app_parent_task_todo", methods={"GET"}, requirements={"root"="\d+"})
     * @Route("/{root}/tasks/reminders", name="app_parent_task_reminders", methods={"GET"}, requirements={"root"="\d+"})
     * @Route("/settings", name="app_settings", methods={"GET"})
     * @Route("/history", name="app_history", methods={"GET"})
     * @Route("/{task}/history", name="app_task_history", methods={"GET"}, requirements={"task"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function tasks(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/tasks/status/{status}", name="app_task_status", methods={"GET"})
     * @Route("/{parent}/tasks/status/{status}", name="app_parent_task_status", methods={"GET"}, requirements={"parent"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function status(String $status): Response
    {
        if (!$this->taskStatusConfig->isStatusSlugExisting($status)) {
            throw $this->createNotFoundException('Page not found!');
        }
        return $this->render('index.html.twig');
    }
}
