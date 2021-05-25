<?php

namespace App\Controller;

use App\Config\TaskStatusConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    public function __construct(TaskStatusConfig $taskStatusConfig)
    {
        $this->taskStatusConfig = $taskStatusConfig;
    }

    /**
     * @Route("", name="app_index", methods={"GET"})
     * @Route("/tasks", name="app_task_index", methods={"GET"})
     * @Route("/tasks/todo", name="app_task_todo", methods={"GET"})
     * @Route("/tasks/reminders", name="app_task_reminders", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/tasks/status/{status}", name="app_task_status", methods={"GET"})
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
